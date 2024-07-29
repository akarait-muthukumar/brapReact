<?php
class Dashboard {

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function ChangeIndFmt($data) {
        return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $data);
    }

    public function getDepartmentCount() {

        $sql = "SELECT count(DeptID) as count FROM departmentwisereformid dw
        INNER JOIN m_department md ON md.department_code = dw.DeptID AND md.m_parent_department_id IS NULL 
        WHERE m_year = {$_POST['year']}  AND dw.is_deleted = 0";

        return $this->db->getRow($sql);
    }

    public function getCompletedSurveyCount() {

        $sql = "SELECT COUNT(fcd.Department) AS count FROM firm_company_details fcd 
        INNER JOIN departmentwisereformid dwf ON dwf.department_id = fcd.department_id AND dwf.is_deleted = 0
        WHERE fcd.deleted = 0 AND fcd.survey_comp_status = 'Completed' AND fcd.Survey_Year = {$_POST['year']}";
        
        return $this->db->getRow($sql);
    }

    public function round_values($value) {
        $split_float = explode('.', $value);

        if (count($split_float) == 2) {
            if (strlen($split_float[1]) > 2) {

                $float_2nd_int = intval($split_float[1][1]);
                if (intval($split_float[1][2]) >= 5) {
                    $float_2nd_int += 1;
                }

                $float_1st_int = intval($split_float[1][0]);

                if ($float_2nd_int == 10) {
                    $float_1st_int += 1;
                    $float_2nd_int = "0";
                }

                $integer_val = intval($split_float[0]);

                if ($float_1st_int == 10) {
                    $integer_val += 1;
                    $float_1st_int = "0";
                }
                $required_data = $integer_val . "." . $float_1st_int . "" . $float_2nd_int;

                return $required_data;
            } else {
                return $value;
            }
        } else {
            return $value;
        }
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


    private function getGroupPerformance($data){

        $return_arr = array();

        foreach($data as $key => $value){

            $local_arr = array();

            $sql= "SELECT mg.m_group_id, mg.group_name, AVG(qf1) AS rating1, AVG(qf2) AS rating2,
            AVG(qf3) AS rating3, AVG(qf4) AS rating4,  mg.reform_number FROM m_group mg 
            LEFT JOIN firm_company_details fcd ON fcd.department_id = mg.department_id AND fcd.deleted = 0
            LEFT JOIN survey_response sr ON sr.firm_company = fcd.firm_comp_id and sr.deleted = 0  AND sr.survey_status ='Completed'
            WHERE mg.department_id = {$value['department_id']} AND mg.is_deleted = 0 AND fcd.Service_Used MEMBER OF(mg.services_name) AND fcd.Reform_Number MEMBER OF(mg.reform_number)
            GROUP BY mg.m_group_id";
    
            $rs = $this->db->getAll($sql);
    
            if($rs){
                foreach ($rs as $index => $val) {
                    $total = $val['rating1'] + $val['rating2'] + $val['rating3'] + $val['rating4'];
        
                    $rating_avg = $total / 4;
        
                    $local_arr[$index]['m_group_id'] = $val['m_group_id'];             
                    $local_arr[$index]['group_name'] = $val['group_name'];             
                    $local_arr[$index]['score'] = $this->ChangeIndFmt($this->round_values($rating_avg * 20));
                }
            }
     
            array_push($return_arr, array('department_id'=>$value['department_id'], 'group'=> $local_arr));
        }

        return $return_arr;
    }

    static function filterGroup($data){
        if($data['is_group'] == 1) {
           return $data;
        }
    }

    public function getDepartmentPeformance() {
    
        $overall_rating = 0;
        $department_arr = array();
        $department_rating_arr = array();

        $sql = "SELECT dpart.department_id, md.m_department_id, dpart.DeptID, dpart.m_year, dpart.DeptName AS department, AVG(qf1) AS rating1, AVG(qf2) AS rating2, AVG(qf3) AS rating3, AVG(qf4) AS rating4, is_group
        , md.m_parent_department_id FROM departmentwisereformid dpart
        LEFT JOIN firm_company_details fcd ON fcd.department_id = dpart.department_id AND fcd.Survey_Year = dpart.m_year AND fcd.deleted = 0
        LEFT JOIN survey_response sr ON sr.firm_company = fcd.firm_comp_id AND sr.deleted = 0 AND sr.surveyID NOT LIKE 'programmer%' AND sr.survey_status ='Completed'
        INNER JOIN m_department md ON md.department_code = dpart.DeptID
        WHERE dpart.m_year = {$_POST['year']} AND dpart.is_deleted = 0
        GROUP BY dpart.department_id
        ORDER BY dpart.DeptName";

        $rs = $this->db->getAll($sql);

        if($rs){
            foreach ($rs as $key => $val) {
                $total = $val['rating1'] + $val['rating2'] + $val['rating3'] + $val['rating4'];
    
                $rating_avg = $total / 4;
    
                $department_arr[$key]['department_id'] = $val['department_id'];             
                $department_arr[$key]['m_year'] = $val['m_year'];             
                $department_arr[$key]['department'] = $val['department'];
                $department_arr[$key]['DeptID'] = $val['DeptID'];
                $department_arr[$key]['score'] = $this->ChangeIndFmt($this->round_values($rating_avg * 20));
                $department_arr[$key]['is_group'] = $val['is_group'];
                $department_arr[$key]['m_department_id'] = $val['m_department_id'];
                $department_arr[$key]['m_parent_department_id'] = $val['m_parent_department_id'];
                array_push($department_rating_arr, $rating_avg);
            }
    
            $overall_rating = $this->round_values((array_sum($department_rating_arr) / count($department_rating_arr)) * 20);

              
            //Filter is_group department only.
            $filter_group = array_filter($department_arr, array('Dashboard', 'filterGroup'));

           
        
            if($filter_group){
                $group_data = $this->getGroupPerformance($filter_group);

                //  Map group data with department array
                foreach($group_data as $key => $val){
                    $index = array_search($val['department_id'], array_column($department_arr, 'department_id'));
                    $department_arr[$index]['group'] = $val['group'];    
                }
            }

                 //Extract sub departments from results 
                 $sub_department_array = array_values(array_filter($department_arr, array('Dashboard', 'extractSubDepartment')));

                 //Extract departments from results 
                 $department_array = array_values(array_filter($department_arr, array('Dashboard', 'extractDepartment')));

               
                    //  Map sub data with department array
                    foreach($sub_department_array as $keys => $values){
                        $sub_index = array_search($values['m_parent_department_id'], array_column($department_array, 'm_department_id'));
                  
                        $department_array[$sub_index]['sub_department'][$keys] = $values;    
                    }
                                
            
    
            return array('list' => $department_array, 'overall_rating' => $overall_rating);
        }
        else{
            return array('list' => array(), 'overall_rating' => 0);
        }
       
    }
}