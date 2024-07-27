<?php
class Report{

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function getReportList(){
        $where = '';

        if(isset($_POST['year']) && !empty($_POST['year'])){
                $where .= " AND Survey_Year = {$_POST["year"]}";
        }

        $department_id = (isset($_POST["sub_department_id"]) && $_POST["sub_department_id"] != -1 && !empty($_POST["sub_department_id"])) ? $_POST["sub_department_id"] : $_POST["department_id"];

        if(!empty($department_id) && $department_id != '-1'){
                $where .= " AND fcd.department_id = '{$department_id}'";
        }

        if(isset($_POST['reform']) && !empty($_POST['reform']) && in_array('-1', $_POST['reform']) == false){
            $reform = implode(', ', $_POST['reform']);
            $where .= " AND Reform_Number  IN ($reform)";
        }

        if(isset($_POST['survey_month']) && !empty($_POST['survey_month'])){
            $where .= " AND date(Survey_Date) BETWEEN date('{$_POST['survey_month'][0]}') AND date('{$_POST['survey_month'][1]}')";
        }

        if(isset($_POST['status'])){
            $fileter=[];
            foreach ($_POST['status'] as $k => $v){
                switch ($v) {
                    case "Completed":
                        $fileter[] = "survey_comp_status = '".trim($v)."'";
                        break;
                    case "Not Interested":
                        $fileter[] = "survey_comp_status = '".trim($v)."'";
                        break;
                    case "InComplete":
                        $fileter[] = "(survey_comp_status IS NULL OR survey_comp_status = '')";
                        break;
                }
            }

            $status = implode(" OR ", $fileter);

            $where .= " AND ($status)";
        }

        $sql = "SELECT fcd.Department, Reform_Number, Name_of_Firm, Mobile_Numer, Contact_Person, 
        Service_Used, E_mail, Date_of_Final_Approval, survey_comp_status, Contact_Address
        FROM `firm_company_details` AS fcd
        WHERE fcd.deleted = 0 {$where} ORDER BY Reform_Number";

        $result = $this->db->getAll($sql);
       
        return $result;
	
    }

}