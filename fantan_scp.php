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

        $split_slash_count = substr_count($text, "\n");

        if ($split_slash_count == 0) {

            $bet_type = "single";
            $bettext = explode("=", $text);
            $bettext = explode("/", $text);

            $messages = [
                'type' => 'text',
                'text' => $bettext[0] . $bettext[1]
            ];
        } else if ($split_slash_count > 0) {

            $reponse_bet = '';
            $bet_type = "multiple";
            $arrKeywords = explode("/", $text);
            $i = 0;
            foreach ($arrKeywords as $element) {

                $i++;
                $bet_string = checkbetstring($element, $code);
                $bet_value = checkbetvalue($element);
                $code = explode("/", $bet_string);
                $bet_text = $code[0];
                $bet_code = $code[1];


                // echo $bet_string;
                if (!$bet_string) {

                    $element_reponse = '# ' . $i . ' รูปแบบการเดิมพันของท่านไม่ถูกต้อง';
                } else if (!is_numeric($bet_value)) {


                    $element_reponse = '# ' . $i . ' ยอดเงินเดิมพันไม่ถูกต้อง';
                } else {
                    $ch = curl_init('http://e-sport.in.th/ssdev/dt/dashboard/api/user_test/profile/' . $userID);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $resultData = json_decode($result, true);
                    $data = $resultData['data'];
                    $user_id = $data['id'];
                    $credit = $data['credit'];

                    $data = array(
                        "user_id" => $user_id,
                        "user_lineid" => $userID,
                        "user_displayname" => $user_displayname,
                        "bet_text" => $bet_text,
                        "value" => $bet_value,
                        "bet_code" => $bet_code
                    );

                    $request = "";

                    foreach ($data as $key => $val) {
                        $request .= $key . "=" . $val . "&";
                    }

                    $request = rtrim($request, "&");

                    $url = 'http://e-sport.in.th/ssdev/dt/dashboard/api/bet_test/logbet_create';

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    echo $response;

                    $element_reponse = '# ' . $i . ' แทง > ' . $bet_text . " จำนวน " . $bet_value;
                }


                $reponse_bet = $reponse_bet . "\n" . $element_reponse;
            }


            $messages = [
                'type' => 'text',
                'text' => " ชื่อผู้ใช้งาน : " . $user_displayname . " " . $reponse_bet . "\r\n" . "💰 ยอดเงินคงเหลือ : " . $credit
            ];
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
