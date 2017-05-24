<?php
/**
 * Created by PhpStorm.
 * User: Nam
 * Date: 2/18/2017
 * Time: 6:09 PM
 */
require_once("config.php");
require_once("const.php");
require_once("loader.php");


/**
 * Store new user to database
 * @param type $password :password
 * @param type $username :username
 */
function connect()
{
    $db = loader::getInstance();
    $mysqli = $db->getConnection();
    if (!$mysqli) {
        die("Connection failed: " . mysqli_connect_error());
    } else
        return $mysqli;
}

function fnQuery($sql)
{
    $db = loader::getInstance();

    if (!$db->getConnection()) {
        die("Connection failed: " . mysqli_connect_error());
        return false;
    } else {
        $res = $db->query($sql);
        return $res;
    }

}

function StoreUser($username, $password, $email, $displayname, $birthday, $gender)
{
    $time = time();
    $mysqli = connect();
    $sql = "INSERT INTO account (username,password,email,displayname,birthday,gender,time_create)
                VALUES (?,?,?,?,?,?,?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssssii", $username, $password, $email, $displayname, $birthday, $gender, $time);

    $result = $stmt->execute();
    $stmt->close();

    if ($result) {
        $sql = "SELECT * FROM account WHERE username = '" . $username . "'";
        $result = $mysqli->query($sql);
        if ($result) {
            $user = mysqli_fetch_assoc($result);
            if ($user) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

/**check username exist
 * @param $username
 * @return bool
 */
function IsUserExisted($username)
{
    $sql = "SELECT * FROM account
                WHERE username = ?";
    $stmt = connect()->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows() > 0) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

function getUsernameById($id)
{
//        $mysqli = connect();
    $sql = "SELECT username
                FROM account
                WHERE id = '" . $id . "'";
    $res = fnQuery($sql);
    if ($res) {
        $temp = mysqli_fetch_assoc($res);
        return $temp[USERNAME];
    } else return true;
}

/**check email exist
 * @param $email
 * @return bool
 */
function IsEmailExisted($email)
{
    $sql = "SELECT * FROM account
                WHERE email = ?";
    $stmt = connect()->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows() > 0) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

function Login($username, $password, $fcm_token)
{
    $sql = "SELECT * FROM account
                where username = '" . $username . "'
                AND password = '" . $password . "'
                LIMIT 1";
    $result = fnQuery($sql);
    if (mysqli_num_rows($result) > 0) {
        do {
            $token = createToken(50);
        } while (IsTokenEsixt($token));
        $data = array();
        $data[TOKEN] = $token;
        //ResponseMessage("11","aa",$data);
        $sql = "UPDATE account
                    SET fcm_token = '" . $fcm_token . "',
                    token = '" . $token . "'

                    WHERE username = '" . $username . "'";
        $result = fnQuery($sql);
        if ($result)
            return $token;
        else
            return false;
    } else
        return false;
}


/** check token exist
 * @param $token
 * @return bool
 */
function IsTokenEsixt($token)
{
//        $mysqli = connect();
    $sql = "SELECT * FROM account
                WHERE token = '" . $token . "'
                LIMIT 1";
    $result = fnQuery($sql);
    if (mysqli_num_rows($result) > 0)
        return true;
    return false;
}

/**
 * @param $code : success or fail
 * @param $message
 * @param $data
 */
function responseMessage($code, $message, $data)
{
    $response = array();
    $response[CODE] = $code;
    $response[MESSAGE] = $message;
    $response[DATA] = $data;
    echo json_encode($response);
}

//create token
function createToken($length)
{
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));
    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;
}

function addFcmToken($username, $fcm_token)
{
//        $mysqli = connect();
    $sql = "UPDATE account
            SET fcm_token = '" . $fcm_token . "'
            WHERE username = '" . $username . "'";
    $result = fnQuery($sql);
    if ($result)
        return true;
    else
        return false;
}


/**check token exist
 * @param $token
 */
function isTokenExist($token)
{

    $sql = "SELECT *
                FROM account
                WHERE token = ?";
    $stmt = connect()->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows() > 0) {
        $stmt->close();
        return true;
    }
    $stmt->close();
    return false;
}

function getListConversation($token)
{
    $array = array();
//        $mysqli = connect();
    $id_username = getIdUsernameByToken($token);
    if ($id_username) {
        $sql = "SELECT *
                    FROM user_in_conversation
                    WHERE id_username = '" . $id_username . "'";
        $result = fnQuery($sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $temp[ID] = $row[ID];
                $temp[LIST_USER] = getListUsernameInConversationById($row[ID]);
                $nameConversation = getNameConversationById($row[ID]);
                if ($nameConversation)
                    $temp[NAME_CONVERSATION] = $nameConversation;
                else {
                    foreach ($temp[LIST_USER] as $user) {
                        if ($user[ID] != $id_username)
                            $temp[NAME_CONVERSATION] = $user[USERNAME];
                    }
                }
                $array[] = $temp;
                //ResponseMessage(CODE_OK,"",$array);
            }
        }
        return $array;
    }
    return false;
}


function getListFriend($token)
{
//        $mysqli = connect();
    $listFriend = array();
    $id_username = getIdUsernameByToken($token);
    if ($id_username) {
        $sql = "SELECT *
                    FROM friend
                    WHERE id_username = '" . $id_username . "'
                    OR id_userfriend = '" . $id_username . "'";
        $res = fnQuery($sql);
        if (mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                if ($row[ID_USERNAME] == $id_username)
                    $infoUser = getInfoUserById($row[ID_USERFRIEND]);
                else
                    $infoUser = getInfoUserById($row[ID_USERNAME]);
                $infoUser["id_friend"] = $row[ID];
                $listFriend[] = $infoUser;
            }
        }
        return $listFriend;
    }
    return false;
}

function getIdFriend($id_username, $id_friend)
{
    $sql = "SELECT id
                    FROM friend
                    WHERE (id_username = '" . $id_username . "'
                    and  id_userfriend = '" . $id_friend . "')
                    or (id_userfriend = '" . $id_username . "'
                    and   id_username= '" . $id_friend . "')";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        return mysqli_fetch_assoc($res)[ID];
    }
    return false;
}

//
function getInfoUserById($id_username)
{
//        $mysqli = connect();
    $data = array();
    $sql = "SELECT * FROM account
                WHERE id = '" . $id_username . "'";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);
        $data[USERNAME] = $user[USERNAME];
        $data[DISPLAYNAME] = $user[DISPLAYNAME];
        $data[GENDER] = $user [GENDER];
        $data[ID] = $user[ID];
        return $data;
    }
    return false;
}

function getNameConversationById($id)
{
//        $mysqli = connect();
    $sql = "SELECT name_conversation
                FROM conversation
                WHERE id = '" . $id . "'";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        $name = mysqli_fetch_assoc($res);
        return $name[NAME_CONVERSATION];
    }
    return false;
}

function getUsernameByToken($token)
{
//        $mysqli = connect();
    $sql = "SELECT username
                FROM account
                WHERE token = '" . $token . "'";
    $result = fnQuery($sql);
    if ($result) {
        $temp = mysqli_fetch_assoc($result);
        if ($temp)
            return $temp[USERNAME];
        return false;
    }
    return false;

}

function getListUsernameInConversationById($id)
{
//        $mysqli = connect();
    $sql = "SELECT *
                FROM user_in_conversation
                WHERE id = '" . $id . "'";
    $result = fnQuery($sql);
    $array = array();
    $temp = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id_username = $row[ID_USERNAME];
            $sql = "SELECT * FROM account WHERE id = '$id_username'";
            $result2 = fnQuery($sql);
            if (mysqli_num_rows($result2) > 0) {
                while ($row2 = mysqli_fetch_assoc($result2)) {
                    $temp[USERNAME] = $row2[USERNAME];
                    $temp[DISPLAYNAME] = $row2[DISPLAYNAME];
                    $temp[GENDER] = $row2[GENDER];
                    $temp[ID] = $id_username;
                    $array[] = $temp;
                }
//                        array_push($array,$temp);
            }
//                return false;
        }
        return $array;
    }
    return false;
}

function getListTokenByIdConversation($conversation_id, $message, $idUsernameSend)
{
    $listFcmToken = array();
    $listUsername = getListUsernameInConversationById($conversation_id);
    if ($listUsername) {
        foreach ($listUsername as $value) {
            $fcmToken = getFcmTokenByIdUsername($value[ID]);

            if ($value[ID] != $idUsernameSend) {

                if ($fcmToken) {
                    array_push($listFcmToken, $fcmToken);
                } else {
                    addMessageToWaitMessage($conversation_id, $message, $idUsernameSend, $value[ID]);
                }
            }
        }
        return $listFcmToken;
    }
    return false;
}

function getIdUsernameByToken($token)
{
//        $mysqli = connect();
    $sql = "SELECT id
                FROM account
                WHERE token = '" . $token . "'";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        $temp = mysqli_fetch_assoc($res);
        return $temp[ID];
    }
    return false;
}

function getTokenByUsername($username)
{
//        $mysqli = connect();
    $sql = "SELECT token
                FROM account
                WHERE username = '" . $username . "'";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        $temp = mysqli_fetch_assoc($res);
        return $temp[TOKEN];
    }
    return false;
}

function getFcmTokenByIdUsername($id_username)
{
    $sql = "SELECT fcm_token
                FROM account
                WHERE id = '" . $id_username . "'";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        $temp = mysqli_fetch_assoc($res);
        if ($temp[FCM_TOKEN] != '')
            return $temp[FCM_TOKEN];
        else
            return false;
    }
    return false;
}

function addMessageToWaitMessage($conversation_id, $message, $idSend, $idReceive)
{
    $sql = "INSERT INTO wait_message
                (conversation_id, message, id_send, id_receive)
                VALUES ('" . $conversation_id . "','" . $message . "','" . $idSend . "','" . $idReceive . "')";
    $res = fnQuery($sql);
    if ($res)
        return true;
    return false;
}

function addRequestFriend($id_request, $id_receive, $message)
{
//        $mysqli = connect();
    if (!isExistRequestFriend($id_request, $id_receive)) {

        $sql = "INSERT INTO wait_request_friend
                (id_request,id_receive,message)
                VALUES ('$id_request','$id_receive','$message')";
        return fnQuery($sql);
    } else return true;
}

function isExistRequestFriend($id_request, $id_receive)
{
    $sql = 'select * from wait_request_friend
            where id_request = ' . $id_request . '
            and id_receive = ' . $id_receive;
    return mysqli_num_rows(fnQuery($sql));
}

function getIdRequestFriend($id_receive, $id_request)
{
    $sql = "SELECT id
               FROM wait_request_friend
               WHERE id_receive = '" . $id_receive . "'
                AND   id_request = '" . $id_request . "'";
//        $mysqli = connect();
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        $temp = mysqli_fetch_assoc($res);
        return $temp[ID];
    } else {
        return false;
    }
}

function deleteRequestFriend($id)
{
    $sql = "DELETE FROM wait_request_friend
                WHERE id = '" . $id . "'";
    $res = fnQuery($sql);
    if ($res)
        return true;
    return false;
}

function insertCodeResponseRequestFriend($id, $code_response)
{
    $sql = "update wait_request_friend set 
              code_response = '" . $code_response . "' 
              where id ='" . $id . "'";
    $res = fnQuery($sql);
    if ($res) {
        return true;
    }
    return false;
}

function addFriend($idUsername, $idFriend)
{
    $sql = "INSERT INTO friend
                (id_username,id_userfriend)
                VALUES ('" . $idUsername . "','" . $idFriend . "')";
    $res = fnQuery($sql);
    if ($res)
        return true;
    else return false;
}

function storeAvatar($avatar, $username)
{
    $sql = "UPDATE account
                SET avatar = '" . $avatar . "'
                WHERE username = '" . $username . "'";
//        $mysqli = connect();
    $res = fnQuery($sql);
    if ($res) {
//            $stmt->close();
        return true;
    } else {
//            $stmt->close();
        return false;
    }
}

function downloadAvatar($username)
{
    $sql = "SELECT avatar
                FROM account
                WHERE username = '" . $username . "'";
//        $mysqli = connect();
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if ($row)
            return $row[AVATAR];
        else
            return false;
    } else
        return false;
}

function addNewConversation($token, $list_id, $name_conversation)
{
    $result = true;
    $time_create = time();
    $id_create = getIdUsernameByToken($token);
    if ($id_create) {

        $sql = "INSERT INTO conversation
                (time_create,name_conversation,id_create)
                VALUES ('" . $time_create . "','" . $name_conversation . "','" . $id_create . "')";
//            $mysqli = connect();
        $res = fnQuery($sql);
        if ($res) {
            $id = getIdConversation($id_create, $time_create);
            $result = $id;
            array_push($list_id, $id_create);
            foreach ($list_id as $id_username) {
                if (!addUserToConversation($id_username, $id))
                    $result = false;
            }
        } else
            return false;

    }
    return $result;
}

/**function get Id conversation by username create and time create
 * @param $id_create : id_username create
 * @param $time_create : time create
 * @return bool
 */
function getIdConversation($id_create, $time_create)
{
    $sql = "SELECT id
                FROM conversation
                WHERE id_create = '" . $id_create . "'
                AND time_create = '" . $time_create . "'";
    $res = fnQuery($sql);
    if ($res) {
        $temp = mysqli_fetch_assoc($res);
        return $temp[ID];
    } else
        return false;
}


/**
 * @param $username
 * @param $id
 * @return bool
 */
function addUserToConversation($id_username, $id)
{
    $sql = "INSERT INTO user_in_conversation 
                (id,id_username)
                VALUES ('" . $id . "','" . $id_username . "') ";
    $res = fnQuery($sql);
    if ($res)
        return true;
    else return false;
}

function getMaxIdConversation()
{
    $sql = "SELECT MAX(id)
                FROM user_in_conversation";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {

        $max = mysqli_fetch_assoc($res);
        return $max["MAX(id)"];
    } else
        return false;
}

function getListRequestFriend($token)
{
    $data = array();
    $temp = array();
    $idUsername = getIdUsernameByToken($token);
    if ($idUsername) {

        $sql = "SELECT id_request,message 
                FROM wait_request_friend
                WHERE id_receive = '" . $idUsername . "'";
        $res = fnQuery($sql);
        if (mysqli_num_rows($res) > 0) {
            while ($user = mysqli_fetch_assoc($res)) {
                $temp = getInfoUserById($user[ID_REQUEST]);
                if ($user[MESSAGE])
                    $temp[MESSAGE] = $user[MESSAGE];
                else $temp[MESSAGE] = "";
                $data[] = $temp;
            }
        }
    }
    return $data;
}

function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
{
    $theta = $longitude1 - $longitude2;
    $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return $kilometers;
}

function getSizeConversation($id)
{
    $sql = "SELECT *
            FROM user_in_conversation
             WHERE id = '" . $id . "'";
    $res = fnQuery($sql);
    if ($res)
        return mysqli_num_rows($res);
    else return false;
}

function deleteFriend($id)
{
    $sql = "delete
            from friend
            WHERE id = '" . $id . "'";
    $res = fnQuery($sql);
    if ($res) {
        if (!isExistIdFriend($id))
            return true;
        else
            return false;
    } else
        return false;
}

function isExistIdFriend($id)
{
    $sql = "select *
            from friend
            WHERE id = '" . $id . "'
       ";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        return true;
    } else
        return false;
}

//function isExistRequestFriend($id_request, $id_receive)
//{
//    $sql = "select *
//            from wait_request_friend
//            where id_request = '" . $id_request . "'
//            and id_receive = '" . $id_receive . "'";
//    $res = fnQuery($sql);
//    return mysqli_num_rows($res) > 0;
//
//}

function searchFriend($name)
{
    $list = array();
    $sql = 'select *
            from account
            where username LIKE 
            "%' . $name . '%" 
            order by username asc';
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        while ($item = mysqli_fetch_assoc($res)) {
            $temp[ID] = $item[ID];
            $temp[USERNAME] = $item[USERNAME];
            $list[] = $temp;

        }
    }

    return $list;
}

function loadStatus1($page, $token)
{
    $listStatus = array();
    if ((int)$page == 0)
        $start = 1;
    else
        $start = (int)($page * NUMBER_EACH_PAGE);
    $end = (int)($start + NUMBER_EACH_PAGE);
    $listId = getListFriend($token);
    if ($listId) {
        $id = "(";
        for ($i = 0; $i < count($listId); $i++) {
            $id = $id . $listId[$i][ID];

            if ($i != count($listId) - 1) {
                $id = $id . ",";
            }
        }
        $id = $id . ")";
        $sql = "select * 
                from status
                where id_username 
                in $id 
                order by time_post desc limit $start,$end";
        $res = fnQuery($sql);
//        mysqli_fetch_assoc($res);
        if (mysqli_num_rows($res) > 0) {
            while ($temp = mysqli_fetch_assoc($res)) {
                $listStatus[] = json_encode($temp);
            }
            return $listStatus;
        }
    } else
        return false;

}


function loadStatus($page, $id)
{
    $list = array();
    $start = (int)($page * NUMBER_EACH_PAGE);
    $end = (int)($start + NUMBER_EACH_PAGE);
    $sql = 'select * from status
            where id_username in 
          
            (select id_username 
            from friend
            WHERE id_userfriend = ' . $id . ')
            or
            id_username in
            (select id_userfriend 
            from friend
            WHERE id_username = ' . $id . '
            )
            order by time_post desc
            limit ' . $start . ',' . NUMBER_EACH_PAGE;
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        while ($temp = mysqli_fetch_assoc($res)) {
            $id_status = $temp[ID];
            $temp[NUMBER_LIKE] = countLike($id_status);
            $temp[NUMBER_COMMENT] = countComment($id_status);
            $is_like = isLikeStatus($id, $id_status);
            if ($is_like)
                $temp[TYPE_LIKE] = $is_like;
            else
                $temp[TYPE_LIKE] = STA_UNLIKE;
            $list[] = $temp;
        }
    }
    return $list;
}

function countLike($id)
{
    $sql = 'select * from status_like
            where status_id = ' . $id . '
            and type_like = ' . STA_LIKE;
    return mysqli_num_rows(fnQuery($sql));
}

function countComment($id)
{
    $sql = 'select * from status_comment
            where status_id = ' . $id;
    return mysqli_num_rows(fnQuery($sql));
}

function loadNewStatus($time, $id)
{
    $list = array();
    $start = (int)(0 * NUMBER_EACH_PAGE);
    $end = (int)($start + NUMBER_EACH_PAGE);
    $sql = "select * 
            from 
            status
            where (id_username in 
            (select id_username 
            from friend
            WHERE id_userfriend = '" . $id . "')
            or
            id_username in
            (select id_userfriend 
            from friend
            WHERE id_username = '" . $id . "'
            ))
            and time_post > " . $time . "
            order by time_post desc
            limit $start,".NUMBER_EACH_PAGE."
            ";
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        while ($temp = mysqli_fetch_assoc($res)) {
            $id_status = $temp[ID];
            $temp[NUMBER_LIKE] = countLike($id_status);
            $temp[NUMBER_COMMENT] = countComment($id_status);
            $is_like = isLikeStatus($id, $id_status);
            if ($is_like)
                $temp[TYPE_LIKE] = $is_like;
            else
                $temp[TYPE_LIKE] = STA_UNLIKE;
            $list[] = $temp;
        }
    }
    return $list;
}

function loadNewStatusFriend($time, $fri_id)
{
    $list = array();
    $start = (int)(0 * NUMBER_EACH_PAGE);
    $end = (int)($start + NUMBER_EACH_PAGE);
    $sql = "select * 
            from 
            status
            where id_username = '" . $fri_id . "'
            and time_post > " . $time . "
            order by time_post desc
            limit $start,$end
            ";
//    echo $sql;
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        while ($item = mysqli_fetch_assoc($res)) {
            $list[] = $item;
        }
    }
    return $list;
}

function addNewStatus($id, $status)
{
    $time = time();
    $sql = "insert into
            status
            (status,id_username,time_post)
            VALUES 
            ('" . $status . "','" . $id . "','" . $time . "')";
    $res = fnQuery($sql);
    if ($res)
        return getIdStatus($time, $id);
    else
        return false;

}

function getIdStatus($time_post, $id_use)
{
    $sql = 'select id from status
            where time_post = ' . $time_post . '
            and id_username = ' . $id_use;
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0)
        return mysqli_fetch_assoc($res)[ID];
    else
        return false;
}

function logout($id)
{
    $sql = 'update account set' .
        ' token = "",' .
        ' fcm_token=""' .
        ' WHERE id = ' . $id;
    return fnQuery($sql);
}

function isExistStatus($id)
{
    $sql = 'select *' .
        ' from status' .
        ' where id = ' . $id;
    $res = fnQuery($sql);
    return mysqli_num_rows($res) > 0;

}

function isLikeStatus($id_username, $id_status)
{
    $sql = 'select * from status_like
            where status_id = ' . $id_status . '
            and id_username = ' . $id_username;
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        return mysqli_fetch_assoc($res)[TYPE_LIKE];
    } else
        return false;
}

function changeLikeStatus($id_username, $id_status)
{
    $id = isLikeStatus($id_username, $id_status);
    $type_like = $id == STA_LIKE ? STA_UNLIKE : STA_LIKE;
    if ($id) {
        $sql = 'update status_like
                set type_like = ' . $type_like . '
                where id_username = ' . $id_username . '
                and status_id = ' . $id_status;
        $res = fnQuery($sql);
        if ($res)
            return true;
        else
            return
                false;
    } else
        return false;
}

function likeStatus($id_username, $id_status)
{
    if (isLikeStatus($id_username, $id_status))
        return changeLikeStatus($id_username, $id_status);
    else {
        $sql = 'insert into status_like
                (id_username,status_id,type_like)
                VALUES 
                (' . $id_username . ',' . $id_status . ',' . STA_LIKE . ')';
        return fnQuery($sql);
    }
}

function getComment($status_id,$page)
{
    $start = (int)($page * NUMBER_EACH_PAGE);
    $list = array();
    $sql = 'select * from status_comment
            where status_id = ' . $status_id.'
            order by time_comment desc
            limit '.$start.','.NUMBER_EACH_PAGE;
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        while ($temp = mysqli_fetch_assoc($res)) {
            $id_username = $temp[ID_USERNAME];
            $user = getInfoUserById($id_username);
            $temp[USERNAME] = $user[USERNAME];
            $temp[DISPLAYNAME] = $user[DISPLAYNAME];
            $list[] = $temp;
        }
    }
    return $list;
}

function addComment($status_id, $use_id, $comment)
{
    $time = time();
    if (isExistStatus($status_id)) {
        $sql = "insert into status_comment
                (status_id,id_username,comment,time_comment)
                VALUES 
                (' ". $status_id ." ',' ". $use_id . "','"  . $comment . "','" . $time . "')";
        $res = fnQuery($sql);
        if ($res) {
            return getIdComment($use_id, $time);
        } else return false;

    } else return false;

}

function getIdComment($use_id, $time_comment)
{
    $sql = 'select id from status_comment
            where id_username = ' . $use_id . '
            and time_comment = ' . $time_comment;
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0)
        return mysqli_fetch_assoc($res)[ID];
    else
        return
            false;
}

function loadStatusById($page, $id, $fri_id)
{
    $list = array();
    $start = (int)($page * NUMBER_EACH_PAGE);
    $end = (int)($start + NUMBER_EACH_PAGE);
    $sql = 'select * from status
            where id_username =' . $fri_id . '
            order by time_post desc
            limit ' . $start . ',' . NUMBER_EACH_PAGE;
    $res = fnQuery($sql);
    if (mysqli_num_rows($res) > 0) {
        while ($temp = mysqli_fetch_assoc($res)) {
            $id_status = $temp[ID];
            $temp[NUMBER_LIKE] = countLike($id_status);
            $temp[NUMBER_COMMENT] = countComment($id_status);
            $is_like = isLikeStatus($id, $id_status);
            if ($is_like)
                $temp[TYPE_LIKE] = $is_like;
            else
                $temp[TYPE_LIKE] = STA_UNLIKE;
            $list[] = $temp;
        }
    }
    return $list;
}

function uploadAvatar($time, $name_file, $target_dir)
{
    $uploadOk = 1;
    $imageFileType = pathinfo($_FILES["$name_file"]["name"], PATHINFO_EXTENSION);
    $name = createToken(3) . $time . "." . $imageFileType;
    $target_file = $target_dir . basename($name);

    if (file_exists($target_file)) {
        $uploadOk = 0;
    }
    if ($_FILES["$name_file"]["size"] > 1024 * 1024) {
        $uploadOk = 0;
    }
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        $uploadOk = 0;
    }
    if ($uploadOk == 0) {
        return false;
    } else {
        if (move_uploaded_file($_FILES["$name_file"]["tmp_name"], $target_file)) {
            resize_image($target_dir, 100, 100, $name);
            resize_image($target_dir, 150, 150, $name);
            return $name;
        } else {
            return false;

        }
    }
}

function resize_image($dir, $new_width, $new_height, $name)
{
    $name_file = $dir . $name;
    $image_info = getimagesize($name_file);
    $type = $image_info[2];
    $new_image = imagecreatetruecolor($new_width, $new_height);
    if ($type == IMAGETYPE_JPEG) {

        $image = imagecreatefromjpeg($name_file);
    } elseif ($type == IMAGETYPE_GIF) {

        $image = imagecreatefromgif($name_file);
    } elseif ($type == IMAGETYPE_PNG) {
        $image = imagecreatefrompng($name_file);
        $background = imagecolorallocate($new_image, 0, 0, 0);
        // remove the black
        imagecolortransparent($new_image, $background);
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
    }
    imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, imagesx($image), imagesy($image));
    if (!file_exists($dir . $new_width . "x" . $new_height) && !is_dir($dir . $new_width . "x" . $new_height)) {
        mkdir($dir . $new_width . "x" . $new_height);
    }
    $new_name = $dir . $new_width . "x" . $new_height . "/" . $name;
    if ($type == IMAGETYPE_JPEG) {
        imagejpeg($new_image, $new_name);
    } elseif ($type == IMAGETYPE_GIF) {
        imagegif($new_image, $new_name);
    } elseif ($type == IMAGETYPE_PNG) {
        imagepng($new_image, $new_name);
    }

}
?>