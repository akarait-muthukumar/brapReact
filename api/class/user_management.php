<?php
class UserManagement{

    public function __construct($conn, $general) {
        global $m_year;
        $this->db = $conn;
        $this->general = $general;
        $this->cur_year = $m_year;
    }

    public function getUserType(){
        $sql = "SELECT m_user_type_id as id, user_type as text FROM m_user_type WHERE is_deleted = 0 AND m_user_type_id <> '10000' ";  

        return $this->db->getAll($sql);
    }

    public function getUserList(){
        $where = "";
        if($_POST['active_login'] == 1){
            $where .= " AND login_key <> ' ' ";
        }

        $sql = "SELECT * FROM user_login WHERE deleted IN (0) AND role <> ' '  AND team != 'Admin' {$where}";  

        return $this->db->getAll($sql);
    }

    public function saveUser(){
        global $md5;
        
        $data = json_decode($_POST['data'], true);

        if(!isset($data['deleted'])){

            $data['hide_nnds_survey'] = 1;
            $m_user_type_id = $data['m_user_type_id'];
    
            $deparment_id = $data['department_id'];
            // Login Url Set........
             
            $user_type_sql = "SELECT user_type, redirect_url FROM m_user_type WHERE m_user_type_id = $m_user_type_id";
            $user_type_result = $this->db->getAll($user_type_sql);
            $data['additional_url'] = $user_type_result[0]['redirect_url'];
            $data['role'] = $user_type_result[0]['user_type'];
    
            if($m_user_type_id == 10){
                
                $reformSQL = "SELECT ReformHeader, DeptID, DeptName FROM departmentwisereformid WHERE m_year = {$this->cur_year} AND department_id='{$deparment_id}'";
    
                $reform = $this->db->getAll($reformSQL);
    
                $data['ReformHeader'] = $reform[0]['ReformHeader'];
                $data['team'] = $reform[0]['ReformHeader'];
                $data['DeptID'] = $reform[0]['DeptID'];
                $data['deptName'] = $reform[0]['DeptName'];
            }

            // password convert to encrypt md5
            $p = $data['password'].$md5;

            $data['old_password'] = $data['password'];
            $data['password'] = md5($p);

        }

        $rs = $this->general->saveData('user_login','id', $data);

        return $rs;
    }

    public function resetLoginKey(){

        $data['login_key'] = "";
        $data['id'] = $_POST['user_id'];

        $update_user_login = $this->general->saveData('user_login','id', $data);

        return $update_user_login;
    }

}