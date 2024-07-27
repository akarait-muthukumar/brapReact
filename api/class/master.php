<?php
class Master{

    public function __construct($conn, $general) {
        global $m_year, $allow_month, $m_year_title;

        $this->db = $conn;
        $this->general = $general;

        $getyear  = $this->getActiveYear();

        $m_year  =  $getyear[0]['m_year'];
        $allow_month = [2,3,4,5,6,7];
        $m_year_title  =  $getyear[0]['m_year_title'];

        $this->cur_year =  $m_year;
    }

    public function getMasterDepartment(){
        $sql = "SELECT md1.m_department_id  , md1.department_name   , md1.department_code ,
        (SELECT JSON_ARRAYAGG(JSON_OBJECT) AS aggregated_json
        FROM (SELECT  JSON_OBJECT('m_department_id' , md2.m_department_id ,'department_name' , md2.department_name , 'department_code', md2.department_code)AS JSON_OBJECT
        FROM m_department md2 WHERE md2.m_parent_department_id = md1.m_department_id )a) AS 'sub'
        FROM m_department md1
        WHERE md1.m_parent_department_id IS  NULL AND md1.is_deleted = 0";
        return $this->db->getAll($sql);
    }

    public function getDepartment(){

        $sql = "SELECT m_department_id as id, department_name as text FROM m_department md
        WHERE md.is_deleted = 0 ORDER BY department_name";

        return $this->db->getAll($sql);

    }

    public function getParentDepartment(){
        $sql = "SELECT m_department_id as id , department_name as text  FROM m_department
        WHERE  m_parent_department_id IS NULL AND is_deleted = 0 ";

       return $this->db->getAll($sql);
    }

    public function getSubDepartment(){

        $where = ''; 
        $leftJoin = ''; 
        $group_by = ''; 

        if(isset($_POST['uploaded_dept_only']) && $_POST['uploaded_dept_only'] == 'not_completed'){
            $leftJoin .= " LEFT JOIN firm_company_details fcd ON fcd.department_id = dw.department_id";
            $where .= " AND fcd.survey_comp_status NOT IN ('Completed','Not Interested') AND fcd.deleted = 0  AND fcd.errStatus = 'Valid' ";
            $group_by = " GROUP BY dw.department_id";
        }   

        if(isset($_POST['uploaded_dept_only']) && $_POST['uploaded_dept_only'] == 'all'){
            $leftJoin .= " LEFT JOIN firm_company_details fcd ON fcd.department_id = dw.department_id";
            $where .= "  AND fcd.deleted = 0  AND fcd.errStatus = 'Valid' ";
            $group_by = " GROUP BY dw.department_id";
        }

        $sql = "SELECT dw.department_id AS id, dw.DeptName AS text
                FROM departmentwisereformid dw {$leftJoin}
                WHERE dw.is_deleted = 0 AND dw.DeptID IN (
                SELECT md.department_code
                FROM m_department md
                WHERE md.m_parent_department_id = (
                SELECT m_department_id AS id
                FROM departmentwisereformid dw
                INNER JOIN m_department md ON md.department_code = dw.DeptID AND md.is_deleted = 0 
                WHERE dw.is_deleted = 0 AND dw.department_id = {$_POST['department_id']})) AND m_year = {$_POST['year']} {$where} {$group_by}";

        $result = $this->db->getAll($sql);
        
        if($result){
            if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                array_unshift($result, array('id'=>-1, 'text'=>'All'));
            }
        } 

       return $result;
    }

    public function saveDepartment(){

        $data = json_decode($_POST['data'], true);
        
        $result = $this->general->saveData("m_department", 'm_department_id',  $data);

        return $result;

    }

    public function getMasterReformNumber(){
        $where = '';
        if(isset($_POST['m_reform_id']) && !empty($_POST['m_reform_id'])){
            $where .= " AND m_reform_id = {$_POST['m_reform_id']} ";
        }

        $sql = "SELECT m_reform_id,reform_number,dept_reform_number, area,sub_area,reform_details,reform_details FROM  m_reform 
        WHERE is_deleted = 0 {$where} ORDER BY reform_number";

        $result  = $this->db->getAll($sql);
        return $result;
 
    }

    public function getReformNumbers(){
        $data = array();
        $sql = "SELECT REPLACE(ReformNumber, '\'', '') AS reform_number  FROM departmentwisereformid  
        WHERE is_deleted = 0 AND  department_id = {$_POST['department_id']}";

        $result =  $this->db->getAll($sql);

        if($result){
          $arr = explode(',', $result[0]['reform_number']);
          foreach($arr as $key => $value){
            array_push($data, array('id'=>$value, 'text'=>$value));
          }
        }
        return $data;
    }

    public function getServices(){

        $sql = "SELECT Service_Used AS id , Service_Used AS text FROM  firm_company_details
        WHERE deleted = 0 AND department_id = {$_POST['department_id']}
        GROUP BY remove_space(Service_Used)
        ORDER BY remove_space(Service_Used)";

        return  $this->db->getAll($sql);

    }

    public function saveReformNumber(){

        $data = json_decode($_POST['data'], true);
        
        $result = $this->general->saveData("m_reform", 'm_reform_id',  $data);

        return $result;

    }

    static function extractSubDepartment($row){
        if(!empty($row['m_parent_department_id'])){
            return $row;
        }
    }

    static function extractDepartment($row){
        if(empty($row['m_parent_department_id'])){
            return $row;
        }
    }

    public function getDeptWiseReformNo(){

        $where = '';

        if(isset($_POST['year']) && !empty($_POST['year'])){
            $where .= " AND m_year = {$_POST['year']} ";
        }

        if(isset($_POST['department_id']) && !empty($_POST['department_id'])){
            $where .= " AND department_id = {$_POST['department_id']} ";
        }

        $sql = "SELECT md.m_department_id, md.m_parent_department_id, dms.department_id, md.department_name, dms.m_year, dms.is_group,
        REPLACE(REPLACE(dms.ReformNumber, '\'', '') , ',', ', ') AS ReformNumber
        FROM departmentwisereformid dms
        INNER JOIN m_department md ON md.department_code = dms.DeptID AND md.is_deleted = 0
        WHERE dms.is_deleted = 0 {$where}";

        $result = $this->db->getAll($sql);
        $department_arr = $result;

        if($result && empty($_POST['department_id'])){
            $department_arr = array_values(array_filter($result, array('Master','extractDepartment')));
            $sub_department_arr = array_values(array_filter($result, array('Master','extractSubDepartment')));
           
            if(count($sub_department_arr) > 0){
                foreach($sub_department_arr as $key => $value){
                    $index = array_search($value['m_parent_department_id'], array_column($department_arr ,'m_department_id'));
                    $department_arr[$index]['sub_department'][$key] = $value;
                }
            }
        }
       
        return $department_arr;

    }

    public function checkDeptAlreadyhaveReformNo(){

        $where = '';

        if(isset($_POST['department_id']) && $_POST['department_id'] != -1){
            $where .= " AND  department_id <> {$_POST['department_id']}";
        }

        $sql = "SELECT department_id FROM departmentwisereformid dms 
        INNER JOIN m_department md ON md.department_code = dms.DeptID AND md.m_department_id = {$_POST['m_department_id']}
        WHERE m_year = {$_POST['year']} AND dms.is_deleted = 0 {$where}";

        return $this->db->getAll($sql);

    }

    public function saveDeptWiseReformNo(){

        $data = json_decode($_POST['data'], true);

        if(!isset($data['is_deleted'])){
            $sql = "SELECT department_code AS DeptID, 
            department_name AS DeptName, SUBSTRING_INDEX(department_name,'-',1) AS ReformHeader
            FROM m_department WHERE m_department_id = {$data['m_department_id']}";
    
            $department_result = $this->db->getAll($sql);
    
            if(!empty($department_result)){
    
                $result = $department_result[0];
                $result['department_id'] = $data['department_id'];
                $result['m_year'] = $this->cur_year;
                $result['ReformNumber'] = $data['reform_number'];
                if(isset($data['is_group'])){
                    $result['is_group'] = $data['is_group'];
                }else{
                    $result['is_group'] = 0;
                }
             
                $rs = $this->general->saveData("departmentwisereformid", 'department_id',  $result);
    
                return $rs;
            }
            else{
                return $department_result;
            }
        }
        else{
            $rs = $this->general->saveData("departmentwisereformid", 'department_id',  $data);
    
            return $rs;
        }
        
       
    }
    
    public function saveYear(){

        $data = json_decode($_POST['data'], true);

        $result = $this->general->saveData("m_year", 'm_year_id',  $data);

        return $result;

    }

    public function getMasterYear(){
        $where = '';

        if(isset($_POST['m_year_id']) && !empty($_POST['m_year_id'])){
            $where .= " AND m_year_id = {$_POST['m_year_id']} ";
        }

        $sql = "SELECT m_year_id, m_year, is_status, is_closed, m_year_title, remarks FROM m_year WHERE is_deleted = 0 {$where}";
        $result =  $this->db->getAll($sql);

        return $result;

    }

    public function activeYear(){
           
        $year = $_POST['year'];

        $sql = "UPDATE m_year SET is_status = 1 WHERE m_year = {$year} ";
        $sql1 = "UPDATE m_year SET is_status = 0 WHERE m_year NOT IN ({$year})";
        $result =  $this->db->Execute($sql);
        $result1 =  $this->db->Execute($sql1);
        return $year;

    }

    public function getActiveYear(){
        $sql = "SELECT m_year, m_year_title FROM  m_year  WHERE is_status = 1 AND is_deleted = 0";
        $result = $this->db->getAll($sql);
       return $result;
    }

    public function saveGroup(){

        $data = json_decode($_POST['data'], true);

        $result = $this->general->saveData("m_group", 'm_group_id',  $data, ['jsonObj' => ['reform_number','services_name']]);

        return $result;
    }

    public function getMasterGroup(){ 

        $where = '';

        if(isset($_POST['m_group_id']) && !empty($_POST['m_group_id'])){
            $where .= " AND m_group_id = {$_POST['m_group_id']} ";
        }

        if(isset($_POST['department_id']) && !empty($_POST['department_id'])){
            $where .= " AND department_id = {$_POST['department_id']} ";
        }

        $sql =  "SELECT  m_group_id , department_id , services_name , group_name , reform_number  FROM  m_group WHERE is_deleted = 0 {$where} ";

        $result = $this->db->getAll($sql);

        return $result;
    }

    public function getGroup(){ 

        $where = '';

        if(isset($_POST['department_id']) && !empty($_POST['department_id'])){
            $where .= " AND department_id = {$_POST['department_id']} ";
        }

        $sql =  "SELECT m_group_id as id , group_name as text FROM  m_group WHERE is_deleted = 0 {$where} ";

        $result = $this->db->getAll($sql);

        if(!empty($result) && isset($_POST['all_value']) && $_POST['all_value'] == true){
            array_unshift($result, array('id'=>-1,'text'=>'All'));
        }

        return  $result;
    }

    function getServiceList(){

        $where = "";

        if(isset($_POST['m_service_id']) && !empty($_POST['m_service_id']) && $_POST['m_service_id'] != -1){
            $where .= " AND m_service_id = {$_POST['m_service_id']}";
        }

        $sql = "SELECT m_service_id , `service_name` , reform_number FROM m_service WHERE is_deleted = 0 {$where} order by `service_name`";

        $result = $this->db->getAll($sql);

        return $result;
    }

    function saveService(){

        $data = json_decode($_POST['data'], true);

        if(isset($data['is_deleted']) && $data['is_deleted'] == 1){
            $result = $this->general->saveData('m_service','m_service_id',$data);
        }
        else{
            $data['reform_number'] = explode("," ,$data['reform_number']);
            $result = $this->general->saveData('m_service','m_service_id',$data, ['jsonObj' => ['reform_number']]);
        }

 
        return $result;
    }

}