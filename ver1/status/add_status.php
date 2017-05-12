<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/10/2017
 * Time: 2:06 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_POST[TOKEN]) && isset($_POST[STATUS])){
    $token = $_POST[TOKEN];
    $status = $_POST[STATUS];
    $id = getIdUsernameByToken($token);
    if ($id){
        $res = addNewStatus($id,$status);
        if ($res)
            ResponseMessage(CODE_OK,"ok",null);
        else
            ResponseMessage(CODE_ERROR,"lỗi",null);
    }
    else{
        ResponseMessage(CODE_ERROR,"token sai",null);
    }
}
else{
    ResponseMessage(CODE_FAIL,"Chưa nhập dữ liệu",null);
}