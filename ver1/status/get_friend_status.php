<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/10/2017
 * Time: 1:44 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_POST[TOKEN]) && isset($_POST[PAGE]) && isset($_POST[ID])) {
    $token = $_POST[TOKEN];
    $page = $_POST[PAGE];
    $fri_id = $_POST[ID];
    $id = getIdUsernameByToken($token);
    $displayname = getInfoUserById($fri_id);
    if ($displayname)
        $displayname = $displayname[DISPLAYNAME];
    else
        $displayname = "";
    if ($id) {
        $res = loadStatusById($page, $id,$fri_id);
        $status = array();
        $status[DATA] = $res;
        $status[DISPLAYNAME] = $displayname;
        responseMessage(CODE_OK, "ok", $status);
    } else {
        responseMessage(CODE_FAIL, "Token sai", null);
    }
} else
    responseMessage(CODE_ERROR, "Chưa nhập dữ liệu", null);