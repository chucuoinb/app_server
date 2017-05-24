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

if (isset($_GET[CONVERSATION_ID]) && isset($_GET[MESSAGE]) && isset($_GET[TOKEN])) {
    $usernameSend = getUsernameByToken($_GET[TOKEN]);
    $idUsernameSend = getIdUsernameByToken($_GET[TOKEN]);
    if ($usernameSend) {
        $data[CONVERSATION_ID] = $_GET[CONVERSATION_ID];
        $data[USERNAME_SEND] = $usernameSend;
        $data[ID_USERNAME] = $idUsernameSend;
        $nameConversation = getNameConversationById($_GET[CONVERSATION_ID]);
        if ($nameConversation) {
            $data[NAME_CONVERSATION] = $nameConversation;
        } else
            $data[NAME_CONVERSATION] = $usernameSend;
        $listToken = getListTokenByIdConversation($_GET[CONVERSATION_ID], $_GET[MESSAGE], $idUsernameSend);
        if ($listToken) {

            $push = new PushToFcm(CODE_MESSAGE, $_GET[MESSAGE], $data);
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
        responseMessage(CODE_FAIL, $_GET[TOKEN], null);
} else
    responseMessage(CODE_FAIL, "fail data", null);
?>