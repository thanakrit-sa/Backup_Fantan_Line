<?php

function linedisplayname($groupID, $userID)
{
    global $access_token;
    $displayName = '';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.line.me/v2/bot/group/' . $groupID . '/member/' . $userID,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer " . $access_token,
            "cache-control: no-cache",
            "postman-token: 6dc09c6b-dd83-81ca-75ed-71ce43b5edd7"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {

        $data = json_decode($response, true);
        $user_displayname =   $data['displayName'];
        return $user_displayname;
    }
}

function create_bet_slash($bet_textSlash, $bet_valueSlash, $bet_code, $userID, $user_displayname)
{
    $ch = curl_init('http://e-sport.in.th/ssdev/fantan/api/user_test/profile/' . $userID);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
    $result = curl_exec($ch);
    curl_close($ch);
    $resultData = json_decode($result, true);
    $data = $resultData['data'];
    $user_id = $data['id'];

    $data = array(
        "user_id" => $user_id,
        "user_lineid" => $userID,
        "user_displayname" => $user_displayname,
        "bet_text" => $bet_textSlash,
        "value" => $bet_valueSlash,
        "bet_code" => $bet_code
    );

    $request = "";

    foreach ($data as $key => $val) {
        $request .= $key . "=" . $val . "&";
    }

    $request = rtrim($request, "&");

    $url = 'http://e-sport.in.th/ssdev/fantan/api/bet_test/logbet_create';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
}

function create_bet_equal($bet_textEqual, $bet_valueEqual, $bet_code, $userID, $user_displayname)
{
    $ch = curl_init('http://e-sport.in.th/ssdev/fantan/api/user_test/profile/' . $userID);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
    $result = curl_exec($ch);
    curl_close($ch);
    $resultData = json_decode($result, true);
    $data = $resultData['data'];
    $user_id = $data['id'];

    $data = array(
        "user_id" => $user_id,
        "user_lineid" => $userID,
        "user_displayname" => $user_displayname,
        "bet_text" => $bet_textEqual,
        "value" => $bet_valueEqual,
        "bet_code" => $bet_code
    );

    $request = "";

    foreach ($data as $key => $val) {
        $request .= $key . "=" . $val . "&";
    }

    $request = rtrim($request, "&");

    $url = 'http://e-sport.in.th/ssdev/fantan/api/bet_test/logbet_create';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
}

function check_Bet($element)
{
    $bet_equal = explode("=", $element);
    $bet_textEqual = $bet_equal[0];
    $bet_valueEqual = $bet_equal[1];
    $bet_slash = explode("/", $element);
    $bet_textSlash = $bet_slash[0];
    $bet_valueSlash = $bet_slash[1];
    $content = file_get_contents('php://input');

    $events = json_decode($content, true);

    foreach ($events['events'] as $event) {

        $userID = $event['source']['userId'];
        $groupID = $event['source']['groupId'];
        $text = $event['message']['text'];
        $replyToken = $event['replyToken'];
        $user_displayname = linedisplayname($groupID, $userID);
    }

    #Check Bet_Code
    if (($bet_textEqual == 1 || $bet_textSlash == 1) || ($bet_textSlash == 1 || $bet_textSlash == 1)) {
        $bet_code = "1001";
    } else if (($bet_textEqual == 2 || $bet_textSlash == 2) || ($bet_textSlash == 2 || $bet_textSlash == 2)) {
        $bet_code = "1002";
    } else if (($bet_textEqual == 3 || $bet_textSlash == 3) || ($bet_textSlash == 3 || $bet_textSlash == 3)) {
        $bet_code = "1003";
    } else if (($bet_textEqual == 4 || $bet_textSlash == 4) || ($bet_textSlash == 4 || $bet_textSlash == 4)) {
        $bet_code = "1004";
    } else {
        $bet_code = "Bet_Code Error";
    }

    $ch = curl_init('http://e-sport.in.th/ssdev/fantan/api/user_test/profile/' . $userID);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
    $result = curl_exec($ch);
    curl_close($ch);
    $resultData = json_decode($result, true);
    $data = $resultData['data'];
    $credit = $data['credit'];

    #Check Symbol
    if (strpos($element, "/") == true) {
        if ($bet_textSlash >= 1 && $bet_textSlash <= 4) {
            if ($bet_valueSlash > $credit) {
                $text = "ยอดเงินคงเหลือไม่เพียงพอ";
            } else {
                create_bet_slash($bet_textSlash, $bet_valueSlash, $bet_code, $userID, $user_displayname);
                $text = "แทง/เดิมพันเลข : " . $bet_textSlash . "\r\n" . "จำนวน : " . $bet_valueSlash . " บาท " . "\r\n" . "Code : " . $bet_code;
            }
        } else if (strlen($bet_textSlash) == 3) {
            $data_split = str_split($bet_textSlash);
            if (($data_split[0] >= 1 && $data_split[0] <= 6) && ($data_split[1] >= 1 && $data_split[1] <= 6) && ($data_split[2] >= 1 && $data_split[2] <= 6)) {
                if ($bet_valueSlash > $credit) {
                    $text = "ยอดเงินคงเหลือไม่เพียงพอ";
                } else {
                    $bet_code = "2".$bet_textSlash;
                    create_bet_slash($bet_textSlash, $bet_valueSlash, $bet_code, $userID, $user_displayname);
                    $text = "แทง/เดิมพันเลข : " . $bet_textSlash . "\r\n" . "จำนวน : " . $bet_valueSlash . " บาท " . "\r\n" . "Code : " . $bet_code;
                }
            } else {
                $text = "การเดิมพันแบบสเปเชียลสามารถกรอกหมายเลขได้เพียง 1-6 เท่านั้น";
            }
        } else if (!$bet_textSlash >= 1 && !$bet_textSlash <= 4) {
            $text = "การเดิมพันแบบปกติสามารถกรอกหมายเลขได้เพียง 1-4 เท่านั้น";
        } else {
            $text = "การเดิมพันของท่านไม่ถูกต้อง";
        }
    } else if (strpos($element, "=") == true) {
        if ($bet_textEqual >= 1 && $bet_textEqual <= 4) {
            if ($bet_valueEqual > $credit) {
                $text = "ยอดเงินคงเหลือไม่เพียงพอ";
            } else {
                create_bet_equal($bet_textEqual, $bet_valueEqual, $bet_code, $userID, $user_displayname);
                $text = "แทง/เดิมพันเลข : " . $bet_textEqual . "\r\n" . "จำนวน : " . $bet_valueEqual . " บาท " . "\r\n" . "Code : " . $bet_code;
            }
        } else if (strlen($bet_textEqual) == 3) {
            $data_split = str_split($bet_textEqual);
            if (($data_split[0] >= 1 && $data_split[0] <= 6) && ($data_split[1] >= 1 && $data_split[1] <= 6) && ($data_split[2] >= 1 && $data_split[2] <= 6)) {
                if ($bet_valueEqual > $credit) {
                    $text = "ยอดเงินคงเหลือไม่เพียงพอ";
                } else {
                    $bet_code = "2".$bet_textEqual;
                    create_bet_equal($bet_textEqual, $bet_valueEqual, $bet_code, $userID, $user_displayname);
                    $text = "แทง/เดิมพันเลข : " . $bet_textEqual . "\r\n" . "จำนวน : " . $bet_valueEqual . " บาท " . "\r\n" . "Code : " . $bet_code;
                }
            } else {
                $text = "การเดิมพันแบบสเปเชียลสามารถกรอกหมายเลขได้เพียง 1-6 เท่านั้น";
            }
        } else if (!$bet_textEqual >= 1 && !$bet_textEqual <= 4) {
            $text = "การเดิมพันแบบปกติสามารถกรอกหมายเลขได้เพียง 1-4 เท่านั้น";
        } else {
            $text = "การเดิมพันของท่านไม่ถูกต้อง";
        }
    } else {
        $text = "การเดิมพันของท่านไม่ถูกต้อง";
    }

    return $text;
}

// --------------------------------------------------------------------------------------------------

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
    $user_displayname = linedisplayname($groupID, $userID);
}


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
                    'text' => "ผู้ใช้งาน : " . $user_displayname . "\r\n" . "UserID : " . $userID . "\r\n" . "ยอดเงินคงเหลือ : " . $credit . " บาท "
                ];
            } else {
                $messages = [
                    'type' => 'text',
                    'text' => "ผู้ใช้งาน : " . $user_displayname . "\r\n" . "🥺 ท่านยังไม่ได้ทำการสมัครสมาชิก" . "\r\n" . "📝 สมัครสมาชิกพิมพ์ : play",
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
                    'text' => "ผู้ใช้งาน : " . $user_displayname . "\r\n" . "🥺 ทำการลงทะเบียนสำเร็จ"
                ];
            }
        } else {
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
                    'text' => "ผู้ใช้งาน : " . $user_displayname . "\r\n" . $response
                ];
            } else {
                $messages = [
                    'type' => 'text',
                    'text' => "ผู้ใช้งาน : " . $user_displayname . "\r\n" . "🥺 ท่านยังไม่ได้ทำการสมัครสมาชิก" . "\r\n" . "📝 สมัครสมาชิกพิมพ์ : play",
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
        }
    } else if ($split_slash_count > 0) {

        $reponse_bet = '';
        $bet_type = "multiple";
        $arrKeywords = explode("\n", $text);
        $i = 1;
        foreach ($arrKeywords as $element) {

            $response = check_Bet($element);
            $reponse_bet = $reponse_bet . " # " . $i . " " . $response . "\r\n";
            $i++;
        }
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
            $response = check_Bet($text);
            $messages = [
                'type' => 'text',
                'text' => "ผู้ใช้งาน : " . $user_displayname . "\r\n" . $reponse_bet
            ];
        } else {
            $messages = [
                'type' => 'text',
                'text' => "ผู้ใช้งาน : " . $user_displayname . "\r\n" . "🥺 ท่านยังไม่ได้ทำการสมัครสมาชิก" . "\r\n" . "📝 สมัครสมาชิกพิมพ์ : play",
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
        // $messages = [
        //     'type' => 'text',
        //     'text' => $user_displayname . "\r\n" . $reponse_bet
        // ];
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
