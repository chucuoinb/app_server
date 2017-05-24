<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/11/2017
 * Time: 12:08 AM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_POST[TOKEN]) && isset($_POST[TIME])){
    $token = $_POST[TOKEN];
    $time = $_POST[TIME];
    $id=getIdUsernameByToken($token);
    if ($id){
        $res = loadNewStatus($time,$id);
        responseMessage(CODE_OK,$time,$res);
    }
    else{
        responseMessage(CODE_FAIL,"Token sai",null);
    }
}
else
    responseMessage(CODE_ERROR,"Chưa nhập dữ liệu",null);