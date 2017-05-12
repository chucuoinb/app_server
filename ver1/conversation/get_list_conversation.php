<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/22/2017
 * Time: 12:14 AM
 */
require_once("../operation/const.php");
require_once("../operation/config.php");
require_once("../operation/loader.php");
require_once("../operation/Operation.php");

if(isset($_GET[TOKEN])){
    $token = $_GET[TOKEN];
    $res = getListConversation($token);
    if($res) {
        ResponseMessage(CODE_OK,"",$res);
    }else
        ResponseMessage(CODE_FAIL, "fail", null);
}