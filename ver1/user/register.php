<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/18/2017
 * Time: 11:12 PM
 */
require_once("../operation/const.php");
require_once("../operation/config.php");
require_once("../operation/loader.php");
require_once("../operation/Operation.php");
//$response = array();
if(isset($_POST[USERNAME]) && isset($_POST[PASSWORD]) && isset($_POST[EMAIL]))
{
    $username = $_POST[USERNAME];
    $password = $_POST[PASSWORD];
    $email = $_POST[EMAIL];
    $display_name = isset($_POST[DISPLAYNAME]) ? $_POST[DISPLAYNAME] : "";
    $birthday = isset($_POST[BIRTHDAY]) ? $_POST[BIRTHDAY] : "";
    $gender = isset($_POST[GENDER]) ? $_POST[GENDER] : 0;
//    $gcm_id = isset($_POST[GCM]) ? $_POST[GCM] : null;
    if(IsUserExisted($username))
    {
        responseMessage(CODE_USER_EXIST,"Tên đăng nhập đã tồn tại",null);
    }else
    {
        if(IsEmailExisted($email))
        {
            responseMessage(CODE_EMAIL_EXIST,"Email đã được đăng kí", null);
        }else{
            if(StoreUser($username, md5($password), $email, $display_name, $birthday, $gender))
            {
                responseMessage(CODE_OK,"",null);
            }else
                responseMessage(CODE_FAIL,"Mạng lỗi",null);
        }
    }
}else
{
    responseMessage(CODE_INVALID,"Chưa nhập dữ liệu",null);
}
?>