<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/13/2017
 * Time: 9:37 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_POST[TOKEN]) && isset($_POST[STATUS_ID])) {
    $token = $_POST[TOKEN];
    $status_id = $_POST[STATUS_ID];
    if (!isExistStatus($status_id)) {
        responseMessage(CODE_FAIL, "Status đã bị xóa", null);

    } else {
        $id = getIdUsernameByToken($token);
        if (likeStatus($id, $status_id))
            responseMessage(CODE_OK, "", null);
        else
            responseMessage(CODE_ERROR, "change like err", null);
    }
}
else
    responseMessage(CODE_ERROR,"",null);