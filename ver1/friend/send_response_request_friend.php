<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/24/2017
 * Time: 10:20 AM
 */
require_once("../operation/const.php");
require_once("../operation/config.php");
require_once("../operation/loader.php");
require_once("../operation/Operation.php");
require_once("../operation/firebase.php");
require_once("../operation/push_to_fcm.php");
$data = array();
if (isset($_POST[TOKEN]) && isset($_POST[ID_RECEIVE]) && isset($_POST[CODE_RESPONSE])) {
    $codeResponse = $_POST[CODE_RESPONSE];
    $idResponse = getIdUsernameByToken($_POST[TOKEN]);
    $id_request = getIdRequestFriend($idResponse, $_POST[ID_RECEIVE]);
    if ($codeResponse == CODE_ACCEPT) {
        addFriend($idResponse, $_POST[ID_RECEIVE]);
    }
    if ($idResponse) {
        $data = getInfoUserById($idResponse);
        $data["id_friend"] = getIdFriend($idResponse, $_POST[ID_RECEIVE]);
        if ($codeResponse = CODE_ACCEPT) {
            $data[CODE] = CODE_ACCEPT;
            $friend = getInfoUserById($_POST[ID_RECEIVE]);
            $friend["id_friend"] = getIdFriend($idResponse, $_POST[ID_RECEIVE]);
        } else {
            $data[CODE] = CODE_REJECT;
            $friend = null;
        }
        $fcmToken = getFcmTokenByIdUsername($_POST[ID_RECEIVE]);
        if ($fcmToken) {
            $pust = new PushToFcm(CODE_RESPONSE_FRIEND, "", $data);
            $pustNotify = $pust->getPut();
            $firebase = new Firebase();
            $listToken = array();
            array_push($listToken, $fcmToken);
            $res = $firebase->send($listToken, $pustNotify);
            if ($res) {
            }
        }
        deleteRequestFriend($id_request);
        ResponseMessage(CODE_OK, "insert, don't send", $friend);
    } else
        ResponseMessage(CODE_ERROR, " token loi", null);
} else
    ResponseMessage(CODE_ERROR, "du lieu loi", null);