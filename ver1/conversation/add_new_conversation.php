<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 4/12/2017
 * Time: 11:12 PM
 */
require_once("../operation/Operation.php");
require_once("../operation/const.php");
$index = 0;
$list_friend = array();
if (isset($_POST[TOKEN]) && isset($_POST[ID_USERFRIEND."0"])) {
    $token = $_POST[TOKEN];
    while (isset($_POST[ID_USERFRIEND . $index])) {
        array_push($list_friend, $_POST[ID_USERFRIEND . $index]);
        $index = (int)$index +1;
    }
    $name = (isset($_POST[NAME_CONVERSATION])) ? $_POST[NAME_CONVERSATION] : "";
    $id = addNewConversation($token, $list_friend, $name);
    if ($id)
        responseMessage(CODE_OK, "ok", $id);
    else
        responseMessage(CODE_ERROR, "error", null);

} else
    responseMessage(CODE_ERROR, "error", null);
