<?php
require_once("../operation/Operation.php");
$target_dir = "uploads/";
$target_file = $target_dir . basename("abcde.png");
if(isset($_POST["avatar"])) {
    $image = $_POST["avatar"];
    file_put_contents($target_file, base64_decode($image));
    ResponseMessage(CODE_OK,"success",null);
//}
}

