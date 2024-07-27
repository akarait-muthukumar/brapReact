<?php
class DailyCallReport{

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function getDailyCallReport(){

        $where = "";
        $group_by = "";
        $fields = "";
        
            
        if($_POST["wise"] == 0){
            $group_by .= "fcd.department_id";
        }
        else if($_POST["wise"] == 1){
            $fields .= " fcd.Reform_Number,";
            $group_by .="fcd.Reform_Number, fcd.department_id";
        }
        else if($_POST["wise"] == 2){
            $fields = " SUBSTRING_INDEX(fcd.done_by, '_', 1) AS emuerator_name,";
            $group_by .="emuerator_name, fcd.department_id";
        }

        if (!isset($_POST["end_date"]) && $_POST["end_date"] == "") {

            $where .= " AND DATE(fcd.done_survey_date) = DATE(STR_TO_DATE('{$_POST["start_date"]}', '%d-%b-%Y')) ";

        } else if ($_POST["start_date"] != $_POST["end_date"]) {
            $where .= " AND DATE(fcd.done_survey_date) BETWEEN DATE(STR_TO_DATE('{$_POST["start_date"]}', '%d-%b-%Y')) AND DATE(STR_TO_DATE('{$_POST["end_date"]}', '%d-%b-%Y')) ";

        } else {
            $where .= " AND DATE(fcd.done_survey_date) = DATE(STR_TO_DATE('{$_POST["start_date"]}', '%d-%b-%Y')) ";
        }
        
        if ($_POST["consolidated"] != 1) {
            $fields .= " DATE_FORMAT(fcd.done_survey_date,'%d-%b-%Y') AS survey_date,";
            $group_by .= " ,DATE(fcd.done_survey_date) ";
        }

        $department_id = $_POST["department_id"];

        if(!empty($_POST["sub_department_id"]) && $_POST["sub_department_id"] != -1){
            $department_id = $_POST["sub_department_id"];
        }
        
        if(!empty($_POST["department_id"]) && $_POST["department_id"] != -1){
            $where .= " AND fcd.department_id = {$department_id} ";
        }

        if(!empty($_POST["interviewer_id"]) && $_POST["interviewer_id"] != -1){
            $where .= " AND SUBSTRING_INDEX(fcd.done_by, '_', 1) = '".$_POST["interviewer_id"]."' ";
        }

    
        $sql = "SELECT *,(completed+notinterested) as total FROM 
        ( SELECT {$fields}  fcd.Department as department, 
            SUM( case when survey_comp_status = 'Completed' then 1 ELSE 0 END ) AS completed, 
            SUM( case when survey_comp_status = 'Not Interested' then 1 ELSE 0 END ) AS notinterested
            FROM firm_company_details fcd           
            WHERE fcd.deleted = 0 {$where} GROUP BY {$group_by}
            ORDER BY fcd.done_survey_date desc, SUBSTRING_INDEX(fcd.done_by, '_', 1)  , fcd.Department ) AS sd 
        WHERE sd.completed != 0 OR sd.notinterested != 0";  
    
        return $this->db->getAll($sql);
	
    }

}