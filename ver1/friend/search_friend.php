<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/8/2017
 * Time: 4:11 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
echo $token;
if (isset($_POST[SEARCH]) && isset($_POST[TOKEN])) {
    $token = $_POST[TOKEN];
    $textSearch = $_POST[SEARCH];
    if (getIdUsernameByToken($token)) {

        $res = searchFriend($textSearch);
        if ($res) {
            ResponseMessage(CODE_OK, "44", $res);
        } else
            ResponseMessage(CODE_FAIL, "33", null);
    } else
        ResponseMessage(CODE_ERROR, "22", null);

} else
    ResponseMessage(CODE_ERROR, "11", null);