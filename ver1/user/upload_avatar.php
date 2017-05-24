<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ver1/operation/Operation.php");
require_once($_SERVER['DOCUMENT_ROOT']."/ver1/operation/const.php");
$target_dir = $_SERVER['DOCUMENT_ROOT']."/ver1/uploads/";
$name = time().createToken(3).".jpg";
$target_file = $target_dir .$name;
if(isset($_POST["avatar"])) {
    $image = $_POST["avatar"];
    file_put_contents($target_file, base64_decode($image));
    resize_image($target_dir,50,50,$name);
    responseMessage(CODE_OK,"success",null);
//}
}

