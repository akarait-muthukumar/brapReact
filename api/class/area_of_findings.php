<?php
class AreaOfFindings{

    public function __construct($conn, $general) {
        $this->db = $conn;
        $this->general = $general;
    }

    public function getDepartmentAF(){
        $where = '';

        if(isset($_POST['year']) && !empty($_POST['year'])){
                $where .= " AND dw.m_year = {$_POST["year"]}";
        }

        if(isset($_POST['month']) && !empty($_POST['month'])){
                $where .= " AND fcd.Month = '{$_POST["month"]}'";
        }

        $sql = "SELECT dw.department_id as id, dw.DeptName as text FROM departmentwisereformid dw 
        INNER JOIN m_department md ON md.department_code = dw.DeptID
        LEFT JOIN firm_company_details fcd ON fcd.department_id = dw.department_id 
        WHERE dw.is_deleted = 0 and m_parent_department_id IS NULL AND fcd.deleted = 0 AND fcd.errStatus = 'Valid' 
        {$where} GROUP BY dw.DeptName ORDER BY dw.DeptName";

        $result = $this->db->getAll($sql);

        if($result){
            //add All value
            if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                    array_unshift($result,  array('id'=>'-1', 'text'=>'All')) ;
            }
        }

        return $result;
    }

    public function getReformNumberAF(){

        $where = '';
        
        $department_id = $_POST["department_id"];

        if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"] && $_POST['sub_department_id'] != '-1')){
                $department_id = $_POST["sub_department_id"];
        }

        if(isset($_POST['department_id']) && !empty($_POST['department_id'])){
                $where .= "  AND department_id = '{$department_id}'";
        }

        if(isset($_POST['year']) && !empty($_POST['year'])){
            $where .= " AND Survey_Year = {$_POST["year"]}";
        }

        if(isset($_POST['month']) && !empty($_POST['month']) && $_POST['month'] != -1){
            $where .= " AND `Month` = '{$_POST["month"]}'";
        }


        $sql = "SELECT DISTINCT Reform_Number as id, Reform_Number as text FROM firm_company_details
        WHERE deleted = 0 AND errStatus = 'Valid' AND category='NA' {$where} ORDER BY Reform_Number";  

        $result = $this->db->getAll($sql);

        if($result){
                //add All value
                if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                        array_unshift($result,  array('id'=>'-1', 'text'=>'All')) ;
                }
        }

        return $result;
      
    }

    
    public function getServiceCategoryAF(){

            $where = '';
            
            $department_id = $_POST["department_id"];

            if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"])){
                    $department_id = $_POST["sub_department_id"];
            }

            if(isset($_POST['year']) && !empty($_POST['year'])){
                    $where .= " AND Survey_Year = {$_POST["year"]}";
            }

            if(isset($_POST['month']) && !empty($_POST['month']) && $_POST['month'] != -1){
                $where .= " AND `Month` = '{$_POST["month"]}'";
            }

            if(isset($_POST['department_id']) && !empty($_POST['department_id']) && $department_id != -1){
                    $where .= " AND department_id = '{$department_id}'";
            }

            if(isset($_POST['reform_number']) && !empty($_POST['reform_number'])){
                    $where .= " AND Reform_Number = {$_POST["reform_number"]}";
            }


            if(isset($_POST['table']) && $_POST['table'] == 'area_of_finding'){
                $result = $this->getServiceCategoryFromAF();
            }
            else{
                $sql = "SELECT DISTINCT Service_Used as id , Service_Used as text FROM firm_company_details
                WHERE deleted = 0 AND errStatus = 'Valid' AND category='NA' $where "; 

                $result = $this->db->getAll($sql);
            }
          

            if($result){
                //add All value
                if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                        array_unshift($result,  array('id'=>'-1', 'text'=>'All')) ;
                }
            }

            return $result;

    }

    private function getServiceCategoryFromAF(){

        $where = '';
        
        $department_id = $_POST["department_id"];

        if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"])){
                $department_id = $_POST["sub_department_id"];
        }

        if(isset($_POST['year']) && !empty($_POST['year'])){
                $where .= " AND `year` = {$_POST["year"]}";
        }

        if(isset($_POST['month']) && !empty($_POST['month']) && $_POST['month'] != -1){
        $where .= " AND `month` = '{$_POST["month"]}'";
        }

        if(isset($_POST['department_id']) && !empty($_POST['department_id']) && $department_id != -1){
                $where .= " AND department_id = '{$department_id}'";
        }

        if(isset($_POST['reform_number']) && !empty($_POST['reform_number'])){
                $where .= " AND reform_number = {$_POST["reform_number"]}";
        }

        $sql = "SELECT DISTINCT service_category AS id , service_category AS text FROM  area_of_findings WHERE is_deleted = 0 $where "; 
        
        return $this->db->getAll($sql);

    }

    public function getAreaFindingList($excel = false){
        global $userDetails;

        $m_user_type_id = $userDetails['m_user_type_id'];

        $where = ''; $fields = "";
        
        $department_id = $_POST["department_id"];

        if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"])){
                $department_id = $_POST["sub_department_id"];
        }

        if(isset($_POST['year']) && !empty($_POST['year'])){
                $where .= " AND af.`year` = {$_POST["year"]}";
        }

        if(isset($_POST['month']) && !empty($_POST['month']) && $_POST['month'] != -1){
            $where .= " AND af.`month` = '{$_POST["month"]}'";
        }

        if(isset($_POST['department_id']) && !empty($_POST['department_id']) && $department_id != -1){
                $where .= " AND af.department_id = '{$department_id}'";
        }

        if(isset($_POST['reform_number']) && !empty($_POST['reform_number']) && $_POST['reform_number'] != -1){
                $where .= " AND af.reform_number = '{$_POST['reform_number']}'";
        }

        if(isset($_POST['service_category']) && !empty($_POST['service_category']) && $_POST['service_category'] != -1){
                $where .= " AND af.service_category = '{$_POST['service_category']}'";
        }

        if(isset($_POST['area_of_findings_id']) && !empty($_POST['area_of_findings_id'])){
                $where .= " AND af.area_of_findings_id = '{$_POST["area_of_findings_id"]}'";
        }

  
        if($m_user_type_id == 10){
                $d_sql = "SELECT department_id FROM departmentwisereformid WHERE is_deleted = 0 AND DeptID = '{$userDetails['DeptID']}' AND m_year = {$_POST["year"]}";
                $department_id = $this->db->getOne($d_sql);

                $where .= " AND af.department_id = '{$department_id}'";
        }

        if(!$excel){
                $fields .= " area_of_findings_id, af.department_id, ";
        }

        $sql = "SELECT {$fields}  dmf.DeptName as department, (CASE WHEN {$m_user_type_id} = '10' THEN mr.dept_reform_number ELSE af.reform_number END) AS reform_number, 
                service_category,REPLACE(`month`, '-','-20') AS `month`,`year`, `area_of_findings`, remarks FROM area_of_findings af 
                LEFT JOIN m_reform mr ON mr.reform_number = af.reform_number AND mr.is_deleted = 0
                LEFT JOIN departmentwisereformid dmf ON dmf.department_id = af.department_id AND dmf.is_deleted = 0
                WHERE af.is_deleted = 0 {$where}"; 

        return $this->db->getAll($sql);
    }

    public function saveAF(){

        $data = json_decode($_POST['data'], true);

        $result = $this->general->saveData("area_of_findings", 'area_of_findings_id',  $data);
        return $result;

    }

}