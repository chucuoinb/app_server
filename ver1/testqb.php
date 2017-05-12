<?php
$APPLICATION_ID = "55509";
$AUTH_KEY = "xx3mnaS8xu6aqmO";
$AUTH_SECRET = "8y5y2sC2CpEYRVa";

// GO TO account you found creditial
$USER_LOGIN = "chucuoinb";
$USER_PASSWORD = "26120710";
$quickbox_api_url = "https://api.quickblox.com";
////// END CREDENTIAL
/// RETRIVE TOKEN
$nonce = rand();
$timestamp = time(); // time() method must return current timestamp in UTC but seems like hi is return timestamp in current time zone
$signature_string = "application_id=" . $APPLICATION_ID . "&auth_key=" . $AUTH_KEY . "&nonce=" . $nonce . "&timestamp=" . $timestamp . "&user[login]=" . $USER_LOGIN . "&user[password]=" . $USER_PASSWORD;
$signature = hash_hmac('sha1', $signature_string, $AUTH_SECRET);

$post_body = http_build_query(array(
    'application_id' => $APPLICATION_ID,
    'auth_key' => $AUTH_KEY,
    'timestamp' => $timestamp,
    'nonce' => $nonce,
    'signature' => $signature,
    'user[login]' => $USER_LOGIN,
    'user[password]' => $USER_PASSWORD
));
$url = $quickbox_api_url . "/session.json";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url); // Full path is - https://api.quickblox.com/session.json
curl_setopt($curl, CURLOPT_POST, true); // Use POST
curl_setopt($curl, CURLOPT_POSTFIELDS, $post_body); // Setup post body
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// Execute request and read response
$response = curl_exec($curl);
// Close connection
curl_close($curl);
$response = json_decode($response, TRUE);


$token = $response['session']['token'];
$post_body = http_build_query(array(
    'user[login]' => 'myusername1',
    'user[password]' => 'Quick88@2016#chef',
    'user[email]' => 'myusername1@mychefbuddy.com',
    'user[external_user_id]' => "1",
    'user[facebook_id]' => '',
    'user[twitter_id]' => '',
    'user[full_name]' => '',
    'user[phone]' => ''
));

$url = $quickbox_api_url . "/users.json";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url); // Full path is - https://api.quickblox.com/session.json
curl_setopt($curl, CURLOPT_POST, true); // Use POST
curl_setopt($curl, CURLOPT_POSTFIELDS, $post_body); // Setup post body
curl_setopt($curl, CURLOPT_HTTPHEADER, array('QB-Token: ' . $token));
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
// Execute request and read response
$response = curl_exec($curl);
curl_close($curl);
$response = json_decode($response, TRUE);
$quickblock_id = $response['user']['id'];
echo $quickblock_id;
