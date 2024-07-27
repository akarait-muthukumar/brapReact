<?php
class DepartmentWise {

    public function __construct($conn, $master) {
        $this->db = $conn;
        $this->master = $master;
        $this->department_id = '';
        $this->year = '';
        $this->m_group_where = '';

        $this->where = '';

        $this->qb_label = array(1,2,3,4,'other','none');

        $this->qb1 = array(1=>0,2=>0,3=>0,4=>0,'other'=>0,'none'=>0);
        $this->qb2 = array(1=>0,2=>0,3=>0,4=>0,'other'=>0,'none'=>0);

        $this->qc1 = 0;
        $this->qd1 = 0;
        $this->qd2 = 0;
        $this->qe1 = 0;

        $this->qf1 = 0;
        $this->qf2 = 0;
        $this->qf3 = 0;
        $this->qf4 = 0;

        $this->completed_survey = 0;

    }

    public function getDepartmentId(){
    
        $sql="SELECT department_id FROM departmentwisereformid WHERE is_deleted = 0 AND DeptID = '{$_POST['DeptID']}' 
        AND m_year = '{$_POST['year']}' LIMIT 1";

        return $this->db->GetOne($sql);

    }

    public function completedSurvey(){

        $sql = "SELECT COUNT(*) As data_count FROM firm_company_details fcd
                WHERE fcd.survey_comp_status ='Completed' AND fcd.department_id ='{$this->department_id}' 
                AND fcd.deleted = 0 AND fcd.Survey_Year = {$this->year} {$this->m_group_where}";

        $rs = $this->db->GetAll($sql);

        if($rs[0]['data_count']){
            $this->completed_survey = $rs[0]['data_count'];
        }
        else{
            $this->completed_survey = 0;
        }

    }

    public function qb1(){

        $sql="SELECT qb1 FROM survey_response sr 
        INNER JOIN firm_company_details fcd ON fcd.firm_comp_id = sr.firm_company 
        WHERE sr.deleted = 0 {$this->where}";

        $rs = $this->db->GetAll($sql);

        foreach ($rs as $key => $val) {

            $rand_arr = explode("," , $val['qb1']);

            for($i=0; $i<count($rand_arr); $i++){

                if(in_array($rand_arr[$i], $this->qb_label)){
                    $this->qb1[$rand_arr[$i]] +=1;
                }

            }
        }
     
    }

    public function qb2(){

        $sql="SELECT qb2 FROM survey_response sr 
        INNER JOIN firm_company_details fcd ON fcd.firm_comp_id = sr.firm_company 
        WHERE sr.deleted=0 {$this->where}";

        $rs = $this->db->GetAll($sql);

        foreach ($rs as $key => $val) {

            $rand_arr = explode("," , $val['qb2']);

            for($i=0; $i<count($rand_arr); $i++){

                if(in_array($rand_arr[$i], $this->qb_label)){
                    $this->qb2[$rand_arr[$i]] +=1;
                }

            }
        }
    }

    public function qc1(){

        $sql="SELECT COUNT(qc1) AS data_count FROM survey_response sr 
        INNER JOIN firm_company_details fcd ON fcd.firm_comp_id = sr.firm_company 
        WHERE sr.deleted=0 AND sr.qc1= 1  {$this->where}";

        $rs = $this->db->GetAll($sql);

        $this->qc1 = $rs[0]['data_count'];
    }

    public function qd1(){

        $sql="SELECT COUNT(qd1) AS data_count FROM survey_response sr 
        INNER JOIN firm_company_details fcd ON fcd.firm_comp_id = sr.firm_company 
        WHERE sr.deleted=0 AND sr.qd1= 1 {$this->where}";

        $rs = $this->db->GetAll($sql);

        $this->qd1 = $rs[0]['data_count'];
    }

    public function qd2(){

        $sql="SELECT COUNT(qd2) AS data_count FROM survey_response sr 
        INNER JOIN firm_company_details fcd ON fcd.firm_comp_id = sr.firm_company 
        WHERE sr.deleted=0 AND sr.qd2= 1 {$this->where}";

        $rs = $this->db->GetAll($sql);

        $this->qd2 = $rs[0]['data_count'];
    }

    public function qe1(){

        $sql="SELECT COUNT(qe1) AS data_count FROM survey_response sr 
        INNER JOIN firm_company_details fcd ON fcd.firm_comp_id = sr.firm_company 
        WHERE sr.deleted=0 AND sr.qe1= 1 {$this->where}";

        $rs = $this->db->GetAll($sql);

        $this->qe1 = $rs[0]['data_count'];
    }

    public function qf(){

        $sql="SELECT AVG(qf1) AS rank1, AVG(qf2) AS rank2, AVG(qf3) AS rank3, AVG(qf4) AS rank4 
        FROM `survey_response` sr  INNER JOIN firm_company_details fcd ON fcd.firm_comp_id = sr.firm_company 
        WHERE sr.`deleted`=0 {$this->where}";

        $rs = $this->db->GetAll($sql);

        $this->qf1  = round($rs[0]['rank1'] * 20, 2);
        $this->qf2  = round($rs[0]['rank2'] * 20, 2);
        $this->qf3  = round($rs[0]['rank3'] * 20, 2);
        $this->qf4  = round($rs[0]['rank4'] * 20, 2);

    }

    public function departmentWiseReport() {

        $this->department_id = $_POST['department_id'];
        $this->year = $_POST['year'];
        $m_group_id = $_POST['m_group_id'];
        $sub_department_id = $_POST['sub_department_id'];

        if($m_group_id != -1){
            $m_group_data = $this->master->getMasterGroup();
            if($m_group_data){
                $this->m_group_where = " AND fcd.Service_Used MEMBER OF('{$m_group_data[0]["services_name"]}') AND fcd.Reform_Number MEMBER OF('{$m_group_data[0]["reform_number"]}')";
            }
        }

        if($sub_department_id != -1){
            $this->department_id = $sub_department_id;
        }

        $this->where = "  AND sr.survey_status = 'Completed' AND sr.surveyID NOT LIKE 'programmer%' AND sr.department_id ='{$this->department_id}' 
        AND fcd.deleted = 0 AND fcd.Survey_Year = {$this->year} {$this->m_group_where} ";

        //completed_survey
            $this->completedSurvey();

            if($this->completed_survey == 0){
                 return array(
                    'overall_score'=>0, 
                    'completed_survey'=>0, 
                    'application_convenience'=>$this->qb1,
                    'tracking_convenience'=>$this->qb2,
                    'performance_rating'=>array('qf1'=>$this->qf1,'qf2'=>$this->qf2,'qf3'=>$this->qf3,'qf4'=>$this->qf4), 
                    'process_convenience' => array('qc1'=>$this->qc1, 'qd1'=>$this->qd1, 'qd2'=>$this->qd2),
                    'timeline_compliance' => 0,
                    'tooltip' => array(
                        'application_convenience'=> 0,
                        'tracking_convenience'=>0,
                        'process_convenience' =>0,
                        'timeline_compliance'=>0,
                        'performance_rating' =>0,
                        'overall_score' =>0,
                    )
                );
            }

        //Application Convenience
            $this->qb1();

        // Payment And Application Status Tracking Convenience
            $this->qb2();

        //Process Convenience - Basic Information
            $this->qc1();

        //Process Convenience - Manual intervention
            $this->qd1();

        //Process Convenience - Manual Submission
            $this->qd2();

        //Timeline compliance qe1
            $this->qe1();

        //Performance Rating Of The Online System
            $this->qf();


        //finally calcualtion

            //qb1 -> (qb1_* / complted_survey) * 100
            foreach($this->qb1  as $key => $val){
                $this->qb1[$key] = round((intval($val) / intval($this->completed_survey)) * 100 , 2);
            }

            //qb2 -> (qb2_* / complted_survey) * 100
            foreach($this->qb2  as $key => $val){
                $this->qb2[$key] = round((intval($val) / intval($this->completed_survey)) * 100 , 2);
            }

            //qc1 -> (qc1 / complted_survey) * 100
            $this->qc1 = round((intval($this->qc1) / intval($this->completed_survey)) * 100 , 2);

            //qd1 -> (qd1 / complted_survey) * 100
            $this->qd1 = round((intval($this->qd1) / intval($this->completed_survey)) * 100 , 2);

            //qd2 -> (qd2 / complted_survey) * 100
            $this->qd2 = round((intval($this->qd2) / intval($this->completed_survey)) * 100 , 2);

            //process_convenience_score -> (qc1 + (100 - qd1) + (100 - qd2)) / 3 
            $process_convenience_score = round(($this->qc1 + (100 - $this->qd1) + (100 - $this->qd2)) / 3 , 2);

            //qe1 -> (qe1 / complted_survey) * 100
            $this->qe1 = round((intval($this->qe1) / intval($this->completed_survey)) * 100 , 2);

            // performance rating -> (qf1 + qf2 + qf3 + qf4) / 4
            $performance_score = round(($this->qf1 + $this->qf2 + $this->qf3 + $this->qf4) / 4, 2);

            // overall_score  (qb1_none + qb2_none + process_convenience_score + qe1 + performance_score) / 5
            $overall_score = round(($this->qb1['none'] + $this->qb2['none'] + $process_convenience_score + $this->qe1 + $performance_score) / 5, 2);

            $process_convenience = array('qc1'=>$this->qc1, 'qd1'=>$this->qd1, 'qd2'=>$this->qd2);

            $performance_rating = array('qf1'=>$this->qf1,'qf2'=>$this->qf2,'qf3'=>$this->qf3,'qf4'=>$this->qf4);

            $tooltip = array(
                'application_convenience'=> $this->qb1['none'],
                'tracking_convenience'=>$this->qb2['none'],
                'process_convenience' => $process_convenience_score,
                'timeline_compliance'=>$this->qe1,
                'performance_rating' => $performance_score,
                'overall_score' => $overall_score,
            );

            $result = array(
                'overall_score'=>$overall_score, 
                'completed_survey'=>$this->completed_survey, 
                'application_convenience'=>$this->qb1,
                'tracking_convenience'=>$this->qb2,
                'performance_rating'=>$performance_rating, 
                'process_convenience' => $process_convenience,
                'timeline_compliance' => $this->qe1,
                'tooltip' => $tooltip,
            );

        return  $result;
    }

}