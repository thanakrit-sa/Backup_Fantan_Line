<?php
include('./config.php');

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


// function checkbettext($text) {

//     $matches = array();
//     preg_match("/^[a-z]+/", $text, $matches);
//     $bet_string = $matches[0]; 


//     $matches_value = array();
//     preg_match('/^[0-9]*$/',$text, $matches_value);
//     $bet_value = $matches_value[0]; 

//     $result = array();
//     $result[0] =  $bet_string;
//     $result[0] =  $bet_value;



// }

function checkbetstring($text1)
{
    $bet_string  = preg_replace("/[^0-9]/", "", $text1);
    return $bet_string;
}



function checkbetvalue($text2)
{

    $bet_value  = preg_replace("/[^0-9]/", "", $text2);
    return $bet_value;
}
// function checkbetvalueAfter($text2)
// {

//     $bet_after  = preg_replace("/[^0-9]/", "", $text2);
//     return $bet_after;
// }

// function checkvalidpattern($text)
// {

//     $result = array();
//     $result = preg_split('/(?<=\D)(?=\d)|\d+\K/', $text);
//     if (count($result) > 2 || count($result) < 2) {
//         return false;
//     } else if (count($result) == 2) {

//         return true;
//     }
// }
