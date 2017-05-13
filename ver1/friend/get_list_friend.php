<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/25/2017
 * Time: 12:09 AM
 */
require_once("../operation/const.php");
require_once("../operation/config.php");
require_once("../operation/loader.php");
require_once("../operation/Operation.php");

if(isset($_GET[TOKEN])){
    $res = getListFriend($_GET[TOKEN]);
    if($res){
        responseMessage(CODE_OK,"success",$res);
    }else
        responseMessage(CODE_FAIL,"fail",null);
}else
    responseMessage(CODE_ERROR,"Du lieu loi",null);