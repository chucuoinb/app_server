<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/6/2017
 * Time: 9:18 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
$index = 0;
$listSuccess = array();
$listFail = array();
$result = true;
if (isset($_POST[TOKEN])) {
    $id = getIdUsernameByToken($_POST[TOKEN]);
    if ($id) {
        while (isset($_POST[ID . $index])) {
            $id = $_POST[ID . $index];
            if (isExistIdFriend($id)) {
                $res = deleteFriend($id);
                if (!$res) {
                    $result = false;
                    array_push($listFail, $id);
                } else {
                    array_push($listSuccess, $id);
                }
            } else {
                $result = false;
            }
            $index++;
        }
        if ($result)
            ResponseMessage(CODE_OK, "ok", $listSuccess);
        else
            ResponseMessage(CODE_FAIL, "failure", $listSuccess);
    } else
        ResponseMessage(CODE_FAIL, "failure", $listSuccess);
} else  ResponseMessage(CODE_ERROR, "failure", null);
