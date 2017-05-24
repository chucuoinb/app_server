<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/24/2017
 * Time: 9:22 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_GET[TOKEN]) && isset($_GET[ID])){
    $token = $_GET[TOKEN];
    $status_id = $_GET[ID];
    $id = getIdUsernameByToken($token);
    if ($id){
        $res = getInfoStatus($status_id,$id);
        if ($res)
            responseMessage(CODE_OK,"",$res);
        else
            responseMessage(CODE_ERROR,"error",null);
    }
}
else
    responseMessage(CODE_ERROR,"Chưa nhập dữ liệu",null);