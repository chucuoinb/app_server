<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/11/2017
 * Time: 12:08 AM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if (isset($_POST[TOKEN]) && isset($_POST[TIME]) && isset($_POST[ID])){
    $token = $_POST[TOKEN];
    $time = $_POST[TIME];
    $fri_id = $_POST[ID];
    $id=getIdUsernameByToken($token);
    if ($id){
        $res = loadNewStatusFriend($time,$fri_id);
        responseMessage(CODE_OK,"ok",$res);
    }
    else{
        responseMessage(CODE_FAIL,"Token sai",null);
    }
}
else
    responseMessage(CODE_ERROR,"Chưa nhập dữ liệu",null);