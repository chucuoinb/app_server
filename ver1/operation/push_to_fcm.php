<?php

/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/20/2017
 * Time: 8:13 PM
 */

/**
 *
 * Class PushToFcm
 * param data: conversation_id and username_send (if code=message)
 * param data: uername_request or username_approve (if code =request friend)
 */
class PushToFcm{
    private $code;
    private $message;
    private $data;

    function __construct($code,$message,$data){
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }
    public function getPut(){
        $result = array();
        $result[PACKAGE][CODE] = $this->code;
        $result[PACKAGE][MESSAGE] = $this->message;
        $result[PACKAGE][DATA] = $this->data;
        return $result;
    }
}

?>