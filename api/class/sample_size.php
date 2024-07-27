<?php
class SampleSize{

    public function __construct($conn, $general, $master) {
        $this->db = $conn;
        $this->general = $general;
        $this->master = $master;
    }

    public function getSampleSizeList(){
       
        $year = $_POST['year'];
        $department_id = $_POST['department_id'];
        $sub_department_id = $_POST['sub_department_id'];
        $month = $_POST['survey_month'];

        $ids = $department_id;

        // check it has sub department
        if(empty($sub_department_id) || $sub_department_id == -1){
            $result =  $this->master->getSubDepartment();
            $sub_department_id = array_column($result, 'id');
            array_push($sub_department_id, $department_id);
            $ids = implode(',', $sub_department_id);
        }
        else if($sub_department_id > 0){
            $ids = $sub_department_id;
        }

        $sql = "SELECT dms.id, fcd.id AS fcd_id, fcd.category, fcd.Service_Used ,  COUNT(firm_comp_id) AS user_count, dms.total_sample_size,  COUNT(CASE WHEN survey_comp_status = 'Completed' THEN survey_comp_status END) AS completed,
        COUNT(CASE WHEN survey_comp_status = 'Not Interested' THEN survey_comp_status END) AS not_interest, dms.survey_status
        FROM firm_company_details  fcd
        LEFT JOIN dept_mapping_ss dms ON dms.service_used = fcd.Service_Used AND dms.department_id IN ({$ids}) AND dms.`month` = '{$month}'
        WHERE fcd.Survey_Year = {$year} AND fcd.department_id IN ({$ids}) AND fcd.errStatus = 'Valid'
        AND fcd.`Month` ='{$month}' AND deleted = 0  GROUP BY remove_space(fcd.Service_Used) ORDER BY fcd.Service_Used";

        return $this->db->getAll($sql);

    }

    public function saveSampleSize(){
       
        $year = $_POST['year'];
        $department_id = $_POST['department_id'];
        $month = $_POST['survey_month'];

        $sql = "SELECT DeptName FROM departmentwisereformid  WHERE department_id = '{$department_id}'";
        $dept_result = $this->db->GetAll($sql);

        $department = $dept_result[0]['DeptName'];
       
        $row = json_decode($_POST['data'], true);
        $len = $row['length'];

        for($i = 1; $i <= intval($len); $i++){

            $data['id'] = $row['id'.$i];

            $sql2 = "SELECT category, Service_Used FROM firm_company_details WHERE id = '{$row['fcd_id'.$i]}' AND deleted = 0;";
            $result2 = $this->db->getRow($sql2);

            $data['total_sample_size'] = $row['total_sample_size'.$i];
            $data['survey_status'] = $row['survey_status'.$i];
            $data['service_used'] = $result2['Service_Used'];
            $data['category'] = $result2['category'];

            $data['m_year'] = $year;
            $data['department_id'] = $department_id;
            $data['department'] = $department;
            $data['month'] = $month;
          
            $result =  $this->general->saveData('dept_mapping_ss','id', $data);
        }

        return $result;

    }

}