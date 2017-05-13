<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/8/2017
 * Time: 4:11 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_POST[SEARCH]) && isset($_POST[TOKEN])) {
    $token = $_POST[TOKEN];
    $textSearch = $_POST[SEARCH];
    if (getIdUsernameByToken($token)) {

        $res = searchFriend($textSearch);
        if ($res) {
            responseMessage(CODE_OK, "44", $res);
        } else
            responseMessage(CODE_FAIL, "33", null);
    } else
        responseMessage(CODE_ERROR, "22", null);

} else
    responseMessage(CODE_ERROR, "11", null);