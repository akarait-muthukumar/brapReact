<?php
class MISStatus{
     
    public function __construct($conn, $master) {
        $this->db = $conn;
        $this->master = $master;
    }

    public function clean($string) {
        $string = html_entity_decode($string);
        if(strpos($string, '-') !== false){
            $string = strtolower(str_replace('-', '_', trim($string))); 
        }
        else{
            $string = strtolower(str_replace(' ', '-', trim($string))); 
        }
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    public function cal_percentage($num_amount, $num_total) {
		 
		$count = ($num_amount / $num_total) * 100;

		if($count < 1){

			$result = number_format($count,2);

	    } else {

			$result = number_format($count,0);

	    }

		return $result;
	}

    static function extractSubDepartment($row){
        if(!empty($row['m_parent_department_id'])){
            return $row;
        }
    }

    static function extractGroup($row){
        if($row['is_group'] == 1){
            return $row;
        }
    }

    static function extractDepartment($row){
        if(empty($row['m_parent_department_id'])){
            return $row;
        }
    }

    public function groupDepartmentWise($department_id){
        $year = $_POST["year"];
        $fcd_where = "";
        $fcd2_where = "";
        $dms_where = "";

        if(isset($_POST["survey_month"]) && !empty($_POST["survey_month"]) && $_POST["survey_month"] != -1){
            $fcd_where .= " AND fcd.Month = '{$_POST["survey_month"]}'";
            $fcd2_where .= " AND fcd2.Month = '{$_POST["survey_month"]}'";
            $dms_where .= " AND dms.month = '{$_POST["survey_month"]}'";
        }

        $sql = "SELECT mg.m_group_id , mg.group_name,
        COUNT(fcd.id) AS total, 
        COUNT(CASE WHEN errStatus='Valid' THEN fcd.id END) AS valid , 
        COUNT(CASE WHEN errStatus='Invalid' THEN fcd.id END) AS invalid,
        (SELECT COUNT(*) FROM firm_company_details fcd2 
        WHERE Survey_Year = {$year} {$fcd2_where}  AND fcd2.survey_comp_status = 'Completed' AND fcd2.department_id = {$department_id} AND fcd2.Service_Used MEMBER OF(mg.services_name) AND fcd2.Reform_Number MEMBER OF(mg.reform_number)) AS completed,
        (SELECT COUNT(*) FROM firm_company_details fcd2 
        WHERE Survey_Year = {$year} {$fcd2_where} AND fcd2.survey_comp_status = 'Not Interested' AND fcd2.department_id = {$department_id} AND fcd2.Service_Used MEMBER OF(mg.services_name) AND fcd2.Reform_Number MEMBER OF(mg.reform_number)) AS not_interested
        FROM m_group mg
        LEFT JOIN firm_company_details fcd ON fcd.department_id = {$department_id}  AND fcd.Survey_Year = {$year} {$fcd_where} AND fcd.Service_Used MEMBER OF(mg.services_name) AND fcd.Reform_Number MEMBER OF(mg.reform_number)
        WHERE mg.is_deleted = 0 AND mg.department_id = {$department_id}  AND fcd.deleted = 0 GROUP BY m_group_id ORDER BY group_name";
        
        $result = $this->db->getAll($sql);

        // For % of Completion and Pending records calculation
        foreach($result as $key => $value){

            $x = intval($value['completed']) + intval($value['not_interested']);
    
            $result[$key]['per_completion'] =  $x  ?  $this->cal_percentage($x, $value['valid']).'% ' : '-' ;

            $result[$key]['pending'] =  intval($value['valid']) - $x;
                
        }

        return $result;         
    }

    public function departmentWise(){
        $year = $_POST["year"];
        $fcd_where = "";
        $fcd2_where = "";
        $dms_where = "";

        if(isset($_POST["survey_month"]) && !empty($_POST["survey_month"]) && $_POST["survey_month"] != -1){
            $fcd_where .= " AND fcd.Month = '{$_POST["survey_month"]}'";
            $fcd2_where .= " AND fcd2.Month = '{$_POST["survey_month"]}'";
            $dms_where .= " AND dms.month = '{$_POST["survey_month"]}'";
        }

        $sql = "SELECT dpart.department_id, md.m_department_id, md.m_parent_department_id, dpart.DeptName as department, is_group,
        COUNT(fcd.id) AS total, 
        COUNT(CASE WHEN errStatus='Valid' THEN fcd.id END) AS valid , 
        COUNT(CASE WHEN errStatus='Invalid' THEN fcd.id END) AS invalid,
        (SELECT SUM(total_sample_size) FROM dept_mapping_ss dms WHERE dms.m_year = {$year} {$dms_where} AND dms.department_id = fcd.department_id) AS sample_size,
        (SELECT COUNT(*) FROM firm_company_details fcd2 
        WHERE Survey_Year = {$year} {$fcd2_where}  AND fcd2.survey_comp_status = 'Completed' AND fcd2.department_id = fcd.department_id ) AS completed,
        (SELECT COUNT(*) FROM firm_company_details fcd2 
        WHERE Survey_Year = {$year} {$fcd2_where} AND fcd2.survey_comp_status = 'Not Interested' AND fcd2.department_id = fcd.department_id ) AS not_interested
        FROM departmentwisereformid dpart
        INNER JOIN m_department md ON md.department_code = dpart.DeptID 
        LEFT JOIN firm_company_details fcd ON fcd.department_id = dpart.department_id AND fcd.deleted = 0 AND fcd.Survey_Year = {$year} {$fcd_where}
        WHERE dpart.m_year = {$year} AND  dpart.is_deleted = 0
        GROUP BY dpart.department_id ORDER BY dpart.DeptName";

        $result = $this->db->getAll($sql);

        $valid_records = array_sum(array_column($result, 'valid'));
        $completed_records = array_sum(array_column($result, 'completed'));
        $not_interested = array_sum(array_column($result, 'not_interested'));

        // For % of Completion and Pending records calculation
        foreach($result as $key => $value){

          $x = intval($value['completed']) + intval($value['not_interested']);

          if(!empty($value['sample_size'])){

            $result[$key]['per_completion'] =  $x  ?  $this->cal_percentage($x, $value['sample_size']).'% ' : '-' ;

            $pending = intval($value['sample_size']) - $x;

            $result[$key]['pending'] =  $pending > 0 ?  $pending : 0;

          }
          else{

            $result[$key]['per_completion'] =  $x  ?  $this->cal_percentage($x, $value['valid']).'% ' : '-' ;

            $result[$key]['pending'] =  intval($value['valid']) - $x;

          }
           
        }

        //Extract group from results 
        $group_array = array_values(array_filter($result, array('MISStatus', 'extractGroup')));

        //Extract sub departments from results 
        $sub_department_array = array_values(array_filter($result, array('MISStatus', 'extractSubDepartment')));
        
        //Extract departments from results 
        $department_array = array_values(array_filter($result, array('MISStatus', 'extractDepartment')));
        
        if(count($group_array) > 0){
            foreach($group_array as $key => $value){
                $group = $this->groupDepartmentWise($value['department_id']);
                $index = array_search($value['is_group'], array_column($department_array ,'is_group'));
                $department_array[$index]['group'][$key] = $group;
            }
        }

        if(count($sub_department_array) > 0){
            foreach($sub_department_array as $key => $value){
                $index = array_search($value['m_parent_department_id'], array_column($department_array ,'m_department_id'));
                $department_array[$index]['sub_department'][$key] = $value;
            }
        }

      
        return array('data'=>$department_array, 'total_completed' => $completed_records,
        'total_not_interested'=> $not_interested, 'total_valid'=> $valid_records);
    }

    public function serviceWise(){
        $year = $_POST["year"];
        $department_id = $_POST["department_id"];
        $sub_department_id = $_POST["sub_department_id"];
        $month = $_POST["survey_month"];

        // where
        $where_smaple_size = '';
        $where_no_records = '';
        $where_completed_survey = '';
        $where_not_interest = '';

        if(!empty($year)){
            $where_smaple_size .= " m_year = '$year'";
            $where_no_records .= "  AND Survey_Year = '$year'";
            $where_completed_survey .= "  AND fcd.Survey_Year = '$year'";
            $where_not_interest .= "  AND fcd.Survey_Year = '$year'";
        }

        if(!empty($department_id) && $department_id != -1){

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

            $where_smaple_size .= " AND department_id IN ($ids) ";
            $where_no_records .= "  AND department_id IN ($ids) ";
            $where_completed_survey .= "  AND fcd.department_id IN ($ids) ";
            $where_not_interest .= "  AND fcd.department_id IN ($ids) ";

        }

        if(!empty($month)){
            $where_smaple_size .= " AND month = '$month'";
            $where_no_records .= "  AND Month = '$month'";
            $where_completed_survey .= "  AND Month = '$month'";
            $where_not_interest .= "  AND Month = '$month'";
        }

        //variables

        $sample_size = array();
        $department_arr = array();
	    $sample_size_count = 0;

        $dep_arr = array();
        $dep_arr_check = array();

        $total_records = array();
        $valid_records = array();
        $invalid_records = array();
        $completed_records = array();
        $not_interested = array();
        

        // get sample size
        $sample_size_SQL ="SELECT SUM(total_sample_size) AS sample_size, service_used , department_id
        FROM dept_mapping_ss 
        WHERE  $where_smaple_size  
        GROUP BY remove_space(service_used) ORDER BY service_used ASC";

        $sample_size_Result = $this->db->getAll($sample_size_SQL);
        
        foreach($sample_size_Result as $key => $data){

            $sample_size[$this->clean($data['service_used'])]=$data['sample_size'];
            $department_arr[$this->clean($data['service_used'])]=$data['department_id'];
			
			$sample_size_count += $data['sample_size'];

        }

        // Total no records
        $total_records_SQL="SELECT COUNT(service_used) AS Data_count, service_used FROM firm_company_details 
        WHERE LENGTH(TRIM(service_used)) > 0 AND deleted = 0 $where_no_records
        GROUP BY remove_space(service_used) ORDER by service_used";

        $total_records_Result = $this->db->getAll($total_records_SQL);

        foreach($total_records_Result as $key => $data){

            array_push($dep_arr_check, $this->clean($data['service_used']));
            array_push($dep_arr, trim($data['service_used']));
            array_push($total_records, $data['Data_count']);

        }

        // Total no.of valid records

        $valid_records_SQL="SELECT COUNT(service_used) AS Data_count, service_used FROM firm_company_details 
        WHERE LENGTH(TRIM(Department)) > 0 AND LENGTH(TRIM(Service_Used)) > 0 AND errStatus='Valid' AND deleted = 0 $where_no_records 
        GROUP BY remove_space(service_used) ORDER by service_used";

        $valid_records_Result = $this->db->getAll($valid_records_SQL);

        foreach($valid_records_Result as $key => $data){

            if(in_array($this->clean($data['service_used']), $dep_arr_check)){
                $pos=array_search($this->clean($data['service_used']), $dep_arr_check);
                $valid_records[$pos]= $data['Data_count'];
            }

        }

        // Total no.of Invalid records

        $invalid_records_SQL="SELECT COUNT(service_used) AS Data_count, service_used FROM firm_company_details 
        WHERE LENGTH(TRIM(service_used)) > 0 AND LENGTH(TRIM(Service_Used)) > 0 AND  errStatus='Invalid' AND deleted = 0 $where_no_records
        GROUP BY remove_space(service_used) ORDER by service_used";

        $invalid_records_Result = $this->db->getAll($invalid_records_SQL);

        foreach($invalid_records_Result as $key => $data){

            if(in_array($this->clean($data['service_used']), $dep_arr_check)){
                $pos=array_search($this->clean($data['service_used']), $dep_arr_check);
                $invalid_records[$pos]= $data['Data_count'];
            }

        }

        // completed survery count

        $completed_survey_SQL="SELECT COUNT(fcd.Service_Used) AS Data_count, fcd.Service_Used as service_used FROM firm_company_details fcd 
        WHERE fcd.deleted = 0 AND fcd.survey_comp_status = 'Completed'  $where_completed_survey 
        GROUP BY remove_space(fcd.Service_Used) ORDER BY fcd.Service_Used";
 
        $completed_survey_Result = $this->db->getAll($completed_survey_SQL);

        foreach($completed_survey_Result as $key => $data){

            if(in_array($this->clean($data['service_used']), $dep_arr_check)){
                $pos=array_search($this->clean($data['service_used']), $dep_arr_check);
                $completed_records[$pos]= $data['Data_count'];
            }

        }

        // Not Interested survery count

        $not_interested_survey_SQL="SELECT COUNT(fcd.Service_Used) AS Data_count, fcd.Service_Used as service_used FROM firm_company_details fcd 
        WHERE fcd.deleted = 0 AND fcd.survey_comp_status = 'Not Interested' $where_not_interest 
        GROUP BY remove_space(fcd.Service_Used) ORDER BY fcd.Service_Used";

        $not_interested_survey_Result = $this->db->getAll($not_interested_survey_SQL);

        foreach($not_interested_survey_Result as $key => $data){

            if(in_array($this->clean($data['service_used']), $dep_arr_check)){
                $pos=array_search($this->clean($data['service_used']), $dep_arr_check);
                $not_interested[$pos]= $data['Data_count'];
            }

        }
        
        $table = array();

        foreach($dep_arr_check as $key => $department){

           $table[$key]['service'] = $dep_arr[$key];
          
           $table[$key]['total'] = $total_records[$key] ? $total_records[$key] : '0';

           $table[$key]['valid'] = $valid_records[$key] ? $valid_records[$key] : '0';

           $table[$key]['invalid'] = $invalid_records[$key] ? $invalid_records[$key] : '0';

           $table[$key]['completed'] = $completed_records[$key] ? $completed_records[$key] : '0';

           $table[$key]['not_interested'] = $not_interested[$key] ? $not_interested[$key] : '0';

           $table[$key]['sample_size'] = $sample_size[$department] ? $sample_size[$department] : '0';

           $table[$key]['department_id'] = $department_arr[$department] ? $department_arr[$department] : '0';

           $x = intval($completed_records[$key]) + intval($not_interested[$key]) ;

           if(isset($sample_size[$department]) && intval($sample_size[$department]) != 0){
             
                $table[$key]['per_completion'] =  $x  ?  $this->cal_percentage($x, $sample_size[$department]).'% ' : '-' ;

                $pending_data = intval($sample_size[$department]) - $x;

                $table[$key]['pending'] =  $pending_data > 0 ?  $pending_data : 0;
           }
           else{
                $x = intval($completed_records[$key]) + intval($not_interested[$key]) ;
                $table[$key]['per_completion'] =  $x  ?  $this->cal_percentage($x, $valid_records[$key]).'% ' : '-' ;

                $table[$key]['pending'] =  intval($valid_records[$key]) - $x;
           }

        }

        return array('data'=>$table, 'total_completed' => array_sum($completed_records),
         'total_not_interested'=> array_sum($not_interested), 'total_valid'=> array_sum($valid_records));
    }

    public function getMISStatus(){

        $department_id = $_POST["department_id"];
       
        if(!empty($department_id) && $department_id != -1){
            return $this->serviceWise();
        }
        else{
            return $this->departmentWise();
        }
       
    }
}