<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/22/2017
 * Time: 9:57 PM
 */
require_once("../operation/firebase.php");
require_once("../operation/push_to_fcm.php");
require_once("../operation/const.php");
require_once("../operation/config.php");
require_once("../operation/loader.php");
require_once("../operation/Operation.php");
$list_fcm_receive_request = array();
$data = array();
if(isset($_POST[ID_RECEIVE]) && isset($_POST[TOKEN])){
    $message = (isset($_POST[MESSAGE]))?$_POST[MESSAGE]:"";
    $idSendRequest = getIdUsernameByToken($_POST[TOKEN]);
    if($idSendRequest){
        if(addRequestFriend($idSendRequest,$_POST[ID_RECEIVE],$message)) {
            $fcm_receive_request = getFcmTokenByIdUsername($_POST[ID_RECEIVE]);

            if ($fcm_receive_request) {
                array_push($list_fcm_receive_request,$fcm_receive_request);
                $data[MESSAGE] = $message;
                $data[USERNAME_REQUEST] = getUsernameById($idSendRequest);
                $data[ID_REQUEST] = $idSendRequest;
                $push = new PushToFcm(CODE_REQUEST_FRIEND, $message, $data);
                $pustNotify = $push->getPut();
                $firebase = new Firebase();
                $res = $firebase->send($list_fcm_receive_request, $pustNotify);
                if ($res) {
                    ResponseMessage(CODE_OK, "success", null);
                } else
                    ResponseMessage(CODE_FAIL, "fail", null);
            }else
                ResponseMessage(CODE_ERROR, "don't have FCM token", null);
        }
        else
            ResponseMessage(CODE_ERROR, "don't add", null);
    }else
        ResponseMessage(CODE_ERROR, "token k ton tai", null);
}else
    ResponseMessage(CODE_ERROR, "duu lieu loi", null);