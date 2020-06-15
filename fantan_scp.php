<?php
include('./config.php');

function checkSymbol_Bet($text)
{
    $bet_equal = explode("=", $text);
    $bet_textEqual = $bet_equal[0];
    $bet_valueEqual = $bet_equal[1];
    $bet_slash = explode("/", $text);
    $bet_textSlash = $bet_slash[0];
    $bet_valueSlash = $bet_slash[1];

    #Check Bet_Code
    if (($bet_textEqual == 1 || $bet_textSlash == 1) || ($bet_textSlash == 1 || $bet_textSlash == 1)) {
        $bet_code = "1";
    } else if (($bet_textEqual == 2 || $bet_textSlash == 2) || ($bet_textSlash == 2 || $bet_textSlash == 2)) {
        $bet_code = "2";
    } else if (($bet_textEqual == 3 || $bet_textSlash == 3) || ($bet_textSlash == 3 || $bet_textSlash == 3)) {
        $bet_code = "3";
    } else if (($bet_textEqual == 4 || $bet_textSlash == 4) || ($bet_textSlash == 4 || $bet_textSlash == 4)) {
        $bet_code = "4";
    } else {
        $bet_code = "Bet_Code Error";
    }

    #Check Symbol
    if (strpos($text, "/") == true) {
        if ($bet_textSlash >= 1 && $bet_textSlash <= 4) {
            $text = " แทง/เดิมพันเลข " . $bet_textSlash . " จำนวน " . $bet_valueSlash . " บาท " . "\r\n" . "Code : " . $bet_code;
        } else if (strlen($bet_textSlash) == 3) {
            $data_split = str_split($bet_textSlash);
            if (($data_split[0] >= 1 && $data_split[0] <= 6) && ($data_split[1] >= 1 && $data_split[1] <= 6) && ($data_split[2] >= 1 && $data_split[2] <= 6)) {
                $text = " แทง/เดิมพันเลข " . $bet_textSlash . " จำนวน " . $bet_valueSlash . " บาท " . "\r\n" . "Code : " . $bet_code;
            } else {
                $text = "การเดิมพันของท่านไม่ถูกต้อง";
            }
        } else {
            $text = "การเดิมพันของท่านไม่ถูกต้อง";
        }
    } else if (strpos($text, "=") == true) {
        if ($bet_textEqual >= 1 && $bet_textEqual <= 4) {
            $text = " แทง/เดิมพันเลข " . $bet_textEqual . " จำนวน " . $bet_valueEqual . " บาท " . "\r\n" . "Code : " . $bet_code;
        } else {
            $text = "การเดิมพันของท่านไม่ถูกต้อง";
        }
    } else {
        $text = "การเดิมพันของท่านไม่ถูกต้อง";
    }

    return $text;
}

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

            if ($text == "id") {
                $ch = curl_init('http://e-sport.in.th/ssdev/fantan/api/user_test/profile/' . $userID);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
                $result = curl_exec($ch);
                curl_close($ch);
                $resultData = json_decode($result, true);
                $data = $resultData['data'];
                $line_id = $data['user_lineid'];
                $credit = $data['credit'];

                if ($line_id == $userID) {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "UserID : " . $userID . "\r\n" . "ยอดเงินคงเหลือ : " . $credit . " บาท "
                    ];
                } else {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "🥺 ท่านยังไม่ได้ทำการสมัครสมาชิก" . "\r\n" . "📝 สมัครสมาชิกพิมพ์ : play",
                        "quickReply" => [
                            "items" => [
                                [
                                    "type" => "action",
                                    "action" => [
                                        "type" => "message",
                                        "label" => "สมัครสมาชิก",
                                        "text" => "play"
                                    ]
                                ]
                            ]
                        ]
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
                        'text' => "😇 ชื่อผู้ใช้นี้เป็นสมาชิกอยู่แล้วว"
                    ];
                } else {
                    $data = array(
                        "user_displayname" => $user_displayname,
                        "fullname" => $user_displayname,
                        "user_lineid" => $userID,
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
                        'text' => "🥺 ทำการลงทะเบียนสำเร็จ"
                    ];
                }
            } else {
                $response = checkSymbol_Bet($text);
                $messages = [
                    'type' => 'text',
                    'text' => $response
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
