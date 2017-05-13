<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 3/16/2017
 * Time: 6:39 PM
 */
require_once("../operation/const.php");
require_once("../operation/config.php");
require_once("../operation/loader.php");
require_once("../operation/Operation.php");
$target_dir = "uploads/";
$target_file = $target_dir . basename("abcde.png");
$image = file_get_contents($target_file);
$string = base64_encode($image);
responseMessage(CODE_OK,"success",$string);