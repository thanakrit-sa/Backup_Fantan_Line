<?php


include('./config.php');
// require_once('./custom/fantan_function.php');

http_response_code(200);

date_default_timezone_set('Asia/Bangkok');
$current_datetime = date("Y-m-d H:i:s");
$content = file_get_contents('php://input');

$events = json_decode($content, true);

foreach ($events['events'] as $event) {

    $userID = $event['source']['userId'];
    $groupID = $event['source']['groupId'];
    $text = $event['message']['text'];
    $replyToken = $event['replyToken'];
    $bettext = explode("=",$text);

    if ($event['type'] == 'follow') {
        $messages = [
            'type' => 'text',
            'text' => "🧐 เริ่มการเดิมพันพิมพ์ : play " . "\r\n" . "💰 เช็กยอดคงเหลือพิมพ์ : id " . "\r\n" . "🤩 วิธีการเดิมพันพิมพ์ : step " . "\r\n" . "⛔️ ยกเลิกการเดิมพันพิมพ์ : x " . "\r\n" . "📑 ประวัติการเดิมพันพิมพ์ : c "
        ];
    }
    if ($event['type'] == 'memberJoined') {
        $messages = [
            'type' => 'text',
            'text' => "ยินดีต้อนรับ : " . $user_displayname . "\r\n" . "🧐 เริ่มการเดิมพันพิมพ์ : play " . "\r\n" . "💰 เช็กยอดคงเหลือพิมพ์ : id " . "\r\n" . "🤩 วิธีการเดิมพันพิมพ์ : step " . "\r\n" . "⛔️ ยกเลิกการเดิมพันพิมพ์ : x " . "\r\n" . "📑 ประวัติการเดิมพันพิมพ์ : c "
        ];
    }
    if ($event['type'] == 'join') {
        $messages = [
            'type' => 'text',
            'text' => "🧐 เริ่มการเดิมพันพิมพ์ : play " . "\r\n" . "💰 เช็กยอดคงเหลือพิมพ์ : id " . "\r\n" . "🤩 วิธีการเดิมพันพิมพ์ : step " . "\r\n" . "⛔️ ยกเลิกการเดิมพันพิมพ์ : x " . "\r\n" . "📑 ประวัติการเดิมพันพิมพ์ : c "
        ];
    }
    if ($event['type'] == 'message' && $event['message']['type'] == 'text') {

        $split_slash_count = substr_count($text, "/");

        if ($split_slash_count == 0) {

            $bet_type = "single";

            $bet_string = checkbetstring($text);
            $bet_value = checkbetvalue($text);
            $code = explode("/", $bet_string);
            $bet_text = $code[0];
            $bet_code = $code[1];

            if ($bet_string == "ข้อมูล") {
                $messages = [
                    'type' => 'text',
                    'text' => "UserID : " . $userID . "\r\n" . "GroupID : " . $groupID
                ];
            } else {
                if (!$bet_string) {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "⛔️ รูปแบบการเดิมพันไม่ถูกต้อง",
                        
                    ];
                } else if (!is_numeric($bet_value)) {

                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "⛔️ ยอดเงินเดิมพันไม่ถูกต้อง",
                        
                    ];
                } else {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "เดิมพัน : " . $bet_text . "\r\n" . "จำนวน : " . $bet_value . " บาท"
                    ];
                }
            }
        } else {

            $reponse_bet = '';
            $bet_type = "multiple";
            $arrKeywords = explode("/", $text);
            $i = 0;
            $messages = [
                'type' => 'text',
                'text' => " ชื่อผู้ใช้งาน : "
            ];
            // foreach ($arrKeywords as $element) {

            //     $i++;
            //     $bet_string = checkbetstring($element, $code);
            //     $bet_value = checkbetvalue($element);
            //     $code = explode("/", $bet_string);
            //     $bet_text = $code[0];
            //     $bet_code = $code[1];


           
            //         $element_reponse = '# ' . $i . ' แทง > ' . $bet_text . " จำนวน " . $bet_value;
                


            //     $reponse_bet = $reponse_bet . "\n" . $element_reponse;
            // }


            // $messages = [
            //     'type' => 'text',
            //     'text' => " ชื่อผู้ใช้งาน : " . $user_displayname . " " . $reponse_bet . "\r\n" . "💰 ยอดเงินคงเหลือ : " . $credit
            // ];
        }
    }
}





    $url = 'https://api.line.me/v2/bot/message/reply';
    $data = [
        'replyToken' => $replyToken,
        'messages' => [$messages],
    ];
    $post = json_encode($data);
    $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);

