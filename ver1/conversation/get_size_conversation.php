<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/1/2017
 * Time: 9:11 AM
 */
require_once("../operation/Operation.php");
require_once("../operation/config.php");
if (isset($_GET[CONVERSATION_ID])) {
    $id = $_GET[CONVERSATION_ID];
    $res = getSizeConversation($id);
    if ($res) {
        responseMessage(CODE_OK, "", $res);
    } else
        responseMessage(CODE_FAIL, "failure", null);
} else
    responseMessage(CODE_FAIL, "failure", null);
