<?php
require_once("operation/const.php");
require_once("operation/config.php");
require_once("operation/loader.php");
require_once("operation/Operation.php");
$target_dir = "uploads/";
$target_file = $target_dir . basename("abcde.png");
//echo getDistanceBetweenPointsNew(21.0091486,105.8204953,21.0076888,105.8269865);
//$list = loadNewStatus(1494426222,1);
//if ($list)
//    responseMessage(CODE_OK,"",$list);
//else
//    responseMessage(CODE_FAIL,"",null);
echo countLike(27);