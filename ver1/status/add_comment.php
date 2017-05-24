<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 5/14/2017
 * Time: 9:52 PM
 */
require_once("../operation/const.php");
require_once("../operation/Operation.php");
$listFcmToken = array();
if (isset($_POST[TOKEN]) && isset($_POST[COMMENT]) && isset($_POST[ID])){
    $token = $_POST[TOKEN];
    $comment = $_POST[COMMENT];
    $status_id = $_POST[ID];
    $id = getIdUsernameByToken($token);
    $user = getInfoUserById($id);
    if ($id){
        $res = addComment($status_id,$id,$comment);

        if ($res){
            $idPostStatus = getIdUsernamePostStatus($status_id);
            $fcm_token = getFcmTokenByIdUsername($idPostStatus);
            if ($fcm_token){
                $listFcmToken[] = $fcm_token;
                $data = array();
                $data[ID_STATUS] = $status_id;
                $data[ID_USERNAME] = $id;
                $data[DISPLAYNAME] = $user[DISPLAYNAME];
                $push = new PushToFcm(CODE_COMMENT, "", $data);
                $pushNotify = $push->getPut();
                $firebase = new Firebase();
                $res = $firebase->send($listFcmToken, $pushNotify);
            }
            responseMessage(CODE_OK,"ok",$res);
        }
        else
            responseMessage(CODE_ERROR,"lỗi",null);
    }
    else{
        responseMessage(CODE_ERROR,"token sai",null);
    }
}
else{
    responseMessage(CODE_FAIL,"Chưa nhập dữ liệu",null);
}