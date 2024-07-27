<?php
class Auth{

    public function __construct($conn, $general) {
        $this->db = $conn;
        $this->general = $general;
    }

    public function login() {
        global $md5;
        $userID = $_POST['userID'];
        $device_key = "";
        $user_password = trim($_POST['user_password']).$md5;
        
        $rand_str="QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0987654321";
        $key ="";
        for($i=0;$i<20;$i++){
            $rand_num = rand(0,(strlen($rand_str)-1));
            $key = $key.$rand_str[$rand_num];
        }

        $user_sql = "SELECT ul.* , mut.user_type, mut.redirect_url FROM user_login ul 
        INNER JOIN m_user_type mut on mut.m_user_type_id = ul.m_user_type_id  
        WHERE userID ='{$userID}'  AND password = MD5('{$user_password}') AND deleted = 0"; 
        
        $user_result = $this->db->getAll($user_sql);
   
        if($user_result){
            $login_key = $user_result[0]['login_key'];
            $login_time = $user_result[0]['login_time'];
       
            if(!empty($login_key) && $user_result[0]['m_user_type_id'] != 10000){
                $already_login ="<b>{$_POST['userID']}</b> user has already logged in on another device. Please log out from the other device before attempting to log in.";
                return array('already_login'=>$already_login);
            }
            else{
                $_SESSION["username"] = $user_result[0]['username'] ;
                $_SESSION["m_user_type_id"] = $user_result[0]['m_user_type_id'] ;
                $_SESSION["user_type"] = $user_result[0]['user_type'] ;
                $_SESSION["user_id"] = $user_result[0]['id'] ;
                $_SESSION["DeptID"] = $user_result[0]['DeptID'] ;
                $_SESSION["deptName"] = $user_result[0]['deptName'] ;
                $_SESSION["redirect_url"] = $user_result[0]['redirect_url'] ;
                $_SESSION["deviceID"] = $userID ;
                $_SESSION["login_Key"] = $key;
    
                $data = array();
                $data['login_time'] = date('Y-m-d H:i:s');
                $data['login_key'] = $key;
                $data['id'] = $user_result[0]['id'];
    
                $today_secs = strtotime(date('Y-m-d'));
                $tom_4AM = $today_secs + (24*60*60)+(4*60*60);
    
                setcookie('deviceID', $userID, $tom_4AM, "/");   // 86400 = 1 day
                setcookie('login_Key', $key, $tom_4AM, "/");   // 86400 = 1 day
                setcookie('m_user_type_id', $user_result[0]['m_user_type_id'], $tom_4AM, "/");   // 86400 = 1 day
    
                $update_user_login = $this->general->saveData('user_login','id', $data);
    
                if($update_user_login){
                    $token = generateJwtToken($_SESSION);
     
                    return array("details"=>$_SESSION, "token"=>$token);
                }
                else{
                    return false;
                }
            }
 
        }
        else{
            return false;
        }

    }


    public function updateActivity(){
        $userDetails = json_decode(base64_decode($_POST['userDetails']), true);

        $data['login_time'] = date('Y-m-d H:i:s');
        $data['id'] = $userDetails['user_id'];

        $update_user_login = $this->general->saveData('user_login','id', $data);

        return $update_user_login;

    }

    public function isLoginKeyValid(){

        $sql = "SELECT count(*) as isValid FROM user_login WHERE userID='{$_POST["deviceID"]}' AND login_key <> ''";

        $result = $this->db->getAll($sql);

        return $result[0]['isValid'];
    }


    public function logout() {
        session_unset();
        session_destroy();
        unset($_SESSION);
        setcookie("deviceID", "",  -1, "/");
        setcookie("login_Key", "",  -1, "/");
        setcookie("m_user_type_id", "",  -1, "/");

        $userDetails = json_decode(base64_decode($_POST['userDetails']), true);
        $data['login_key'] = "";
        $data['id'] = $userDetails['user_id'];

        $update_user_login = $this->general->saveData('user_login','id', $data);

        if($update_user_login){
            return true;
        }
        else{
            return false;
        }
    }

}
