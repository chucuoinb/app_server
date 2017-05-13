<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/11/2017
 * Time: 6:09 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_POST[TOKEN])) {
    $token = $_POST[TOKEN];
    $id = getIdUsernameByToken($token);
    if ($id) {
        if (logout($id))
            responseMessage(CODE_OK, "", null);
        else
            responseMessage(CODE_FAIL, "Token sai", null);
    } else {
        responseMessage(CODE_OK, "Token sai", null);
    }
} else
    responseMessage(CODE_ERROR, "Chưa nhập dữ liệu", null);