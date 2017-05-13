<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/20/2017
 * Time: 8:54 PM
 */
require_once("../operation/firebase.php");
require_once("../operation/push_to_fcm.php");

require_once("../operation/const.php");
require_once("../operation/loader.php");
require_once("../operation/Operation.php");
$response = array();
$data = array();

if (isset($_POST[CONVERSATION_ID]) && isset($_POST[MESSAGE]) && isset($_POST[TOKEN])) {
    $usernameSend = getUsernameByToken($_POST[TOKEN]);
    $idUsernameSend = getIdUsernameByToken($_POST[TOKEN]);
    if ($usernameSend) {
        $data[CONVERSATION_ID] = $_POST[CONVERSATION_ID];
        $data[USERNAME_SEND] = $usernameSend;
        $data[ID_USERNAME] = $idUsernameSend;
        $nameConversation = getNameConversationById($_POST[CONVERSATION_ID]);
        if ($nameConversation) {
            $data[NAME_CONVERSATION] = $nameConversation;
        } else
            $data[NAME_CONVERSATION] = $usernameSend;
        $listToken = getListTokenByIdConversation($_POST[CONVERSATION_ID], $_POST[MESSAGE], $idUsernameSend);
        if ($listToken) {

            $push = new PushToFcm(CODE_MESSAGE, $_POST[MESSAGE], $data);
            $pushNotify = $push->getPut();
            $firebase = new Firebase();
            $res = $firebase->send($listToken, $pushNotify);
            if ($res) {
                responseMessage(CODE_OK, "OK", null);
            } else
                responseMessage(CODE_ERROR, "error", null);
        } else
            responseMessage(CODE_OK, "ok", null);
    }else
        responseMessage(CODE_FAIL, $_POST[TOKEN], null);
} else
    responseMessage(CODE_FAIL, "fail data", null);
?>