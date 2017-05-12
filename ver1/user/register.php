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
    $display_name = isset($_POST[DISPLAYNAME]) ? $_POST[DISPLAYNAME] : null;
    $birthday = isset($_POST[BIRTHDAY]) ? $_POST[BIRTHDAY] : null;
    $gender = isset($_POST[GENDER]) ? $_POST[GENDER] : null;
//    $gcm_id = isset($_POST[GCM]) ? $_POST[GCM] : null;
    if(IsUserExisted($username))
    {
        ResponseMessage(CODE_USER_EXIST,"user exist",null);
    }else
    {
        if(IsEmailExisted($email))
        {
            ResponseMessage(CODE_EMAIL_EXIST,"email exist", null);
        }else{
            if(StoreUser($username, $password, $email, $display_name, $birthday, $gender))
            {
                ResponseMessage(CODE_OK,"",null);
            }else
                ResponseMessage(CODE_FAIL,"register failed",null);

        }
    }
}else
{
    ResponseMessage(CODE_INVALID,"invalid",null);
}
?>