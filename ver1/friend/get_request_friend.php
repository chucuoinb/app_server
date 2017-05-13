<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 4/17/2017
 * Time: 9:17 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
if(isset($_GET[TOKEN])){
    $token = $_GET[TOKEN];
    $res = getListRequestFriend($token);
        responseMessage(CODE_OK,"success",$res);
}
else{
    responseMessage(CODE_ERROR,"error",null);
}