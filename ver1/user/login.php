<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/19/2017
 * Time: 12:09 AM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
$data = array();
if(isset($_POST[USERNAME]) && isset($_POST[PASSWORD]) && isset($_POST[FCM_TOKEN])){
    $username = $_POST[USERNAME];
    $password = $_POST[PASSWORD];
    $fcm_token = $_POST[FCM_TOKEN];
    if(IsUserExisted($username))
    {
        $token = Login($username,md5($password),$fcm_token);
        if($token)
        {
            $id = getIdUsernameByToken($token);
            $data = getInfoUserById($id);
            $data[TOKEN] = $token;
            responseMessage(CODE_OK,"",$data);
        }else
            responseMessage(CODE_FAIL,"not match",null);
    }else
        responseMessage(CODE_USER_NOT_EXIST,"username exist",null);
}
else{
    responseMessage(CODE_USER_NOT_EXIST,"Thiếu dữ liệu",null);
}
?>