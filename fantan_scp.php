<?php
include('./config.php');

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

    if ($event['type'] == 'message' && $event['message']['type'] == 'text') {

        $split_slash_count = substr_count($text, "\n");

        if ($split_slash_count == 0) {

            if (strpos($text, "=") == true) {
                $bet_data = explode("=", $text);
                $bet_text = $bet_data[0];
                $bet_value = $bet_data[1];
                if ($bet_text >= 1 && $bet_text <= 4) {
                    $messages = [
                        'type' => 'text',
                        'text' => " แทง/เดิมพันเลข " . $bet_text . " จำนวน " . $bet_value . " บาท "
                    ];
                } else {
                    $messages = [
                        'type' => 'text',
                        'text' => "รูปแบบการเดิมพันของท่านไม่ถูกต้อง"
                    ];
                }
            } else if (strpos($text, "/") == true) {
                $bet_data = explode("/", $text);
                $bet_text = $bet_data[0];
                $bet_value = $bet_data[1];
                if ($bet_text >= 1 && $bet_text <= 4) {
                    $messages = [
                        'type' => 'text',
                        'text' => " แทง/เดิมพันเลข " . $bet_text . " จำนวน " . $bet_value . " บาท "
                    ];
                } else {
                    $messages = [
                        'type' => 'text',
                        'text' => "รูปแบบการเดิมพันของท่านไม่ถูกต้อง"
                    ];
                }
            } else if ($text == "play") {
                $ch = curl_init('http://e-sport.in.th/ssdev/fantan/api/user_test/profile/' . $userID);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
                $result = curl_exec($ch);
                curl_close($ch);
                $resultData = json_decode($result, true);
                $data = $resultData['data'];
                $line_id = $data['user_lineid'];
                if ($line_id == $userID) {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "😇 ชื่อผู้ใช้นี้เป็นสมาชิกอยู่แล้ว"
                    ];
                } else {
                    $data = array(
                        "user_displayname" => "test",
                        "fullname" => "tet",
                        "user_lineid" => "Ua582d60103c545097c800934d714246b",
                    );

                    $data_register = json_encode($data);

                    $ch = curl_init('http://e-sport.in.th/ssdev/fantan/api/user_test/register');

                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_register);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

                    $result = curl_exec($ch);
                    curl_close($ch);

                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "✅ ทำการลงทะเบียนสำเร็จ ✅" . "\r\n"
                    ];
                }
            } else {
                $messages = [
                    'type' => 'text',
                    'text' => "รูปแบบการเดิมพันของท่านไม่ถูกต้อง"
                ];
            }
        } else if ($split_slash_count > 0) {

            $reponse_bet = '';
            $bet_type = "multiple";
            $arrKeywords = explode("\n", $text);
            $i = 1;
            foreach ($arrKeywords as $element) {

                if (strpos($element, "=") == true) {
                    $bet_data = explode("=", $element);
                    $bet_text = $bet_data[0];
                    $bet_value = $bet_data[1];
                    if ($bet_text >= 1 && $bet_text <= 4) {
                        $res_bet = "#" . $i . " แทง/เดิมพันเลข " . $bet_text . " จำนวน " . $bet_value . " บาท " . "\r\n";
                    } else {
                        $res_bet = "#" . $i . " รูปแบบการเดิมพันของท่านไม่ถูกต้อง ";
                    }
                } else if (strpos($element, "/") == true) {
                    $bet_data = explode("/", $element);
                    $bet_text = $bet_data[0];
                    $bet_value = $bet_data[1];
                    if ($bet_text >= 1 && $bet_text <= 4) {
                        $res_bet = "#" . $i . " แทง/เดิมพันเลข " . $bet_text . " จำนวน " . $bet_value . " บาท " . "\r\n";
                    } else {
                        $res_bet = "#" . $i . " รูปแบบการเดิมพันของท่านไม่ถูกต้อง ";
                    }
                } else {
                    $res_bet = "#" . $i . " รูปแบบการเดิมพันของท่านไม่ถูกต้อง ";
                }
                $reponse_bet = $reponse_bet . $res_bet;
                $i++;
            }

            $messages = [
                'type' => 'text',
                'text' => $reponse_bet
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
