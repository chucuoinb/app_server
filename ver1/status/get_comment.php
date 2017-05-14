<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/14/2017
 * Time: 9:48 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_POST[TOKEN]) && isset($_POST[PAGE]) && isset($_POST[ID])) {
    $token = $_POST[TOKEN];
    $page = $_POST[PAGE];
    $status_id = $_POST[ID];
    $id = getIdUsernameByToken($token);
    if ($id) {
        $res = getComment($status_id, $page);
        responseMessage(CODE_OK, "ok", $res);
    } else {
        responseMessage(CODE_FAIL, "Token sai", null);
    }
} else
    responseMessage(CODE_ERROR, "Chưa nhập dữ liệu", null);