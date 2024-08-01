<?php
class Survey{

        public function __construct($conn, $general) {
                $this->db = $conn;
                $this->general = $general;
                $this->userID = $_POST['deviceID'];
                $this->loginKey = $_POST['loginKey'];
        }

        public function getInterviewerList(){
              
                $sql = "SELECT userID AS id, username AS text FROM user_login WHERE deleted IN (0) AND m_user_type_id = 1";  
                $result = $this->db->getAll($sql);

                if($result){
                        //add All value
                        if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                                array_unshift($result,  array('id'=>'-1', 'text'=>'All')) ;
                        }
                }

                return $result;
        }

        public function getDepartmentList(){
                $leftJoin = '';
                $where = '';

                if(isset($_POST['year']) && !empty($_POST['year'])){
                        $where .= " AND dw.m_year = {$_POST["year"]}";
                }

                if(isset($_POST['month']) && !empty($_POST['month'])){
                        $where .= " AND fcd.Month = {$_POST["month"]}";
                }

                if(isset($_POST['uploaded_dept_only']) && $_POST['uploaded_dept_only'] == 'not_completed'){
                        $leftJoin .= " LEFT JOIN firm_company_details fcd ON fcd.department_id = dw.department_id";
                        $where .= " AND fcd.survey_comp_status NOT IN ('Completed','Not Interested') AND fcd.deleted = 0  AND fcd.errStatus = 'Valid' ";
                }

                if(isset($_POST['uploaded_dept_only']) && $_POST['uploaded_dept_only'] == 'all'){
                        $leftJoin .= " LEFT JOIN firm_company_details fcd ON fcd.department_id = dw.department_id";
                        $where .= "  AND fcd.deleted = 0  AND fcd.errStatus = 'Valid' ";
                }

                $sql = "SELECT dw.department_id as `value`, dw.DeptName as label FROM departmentwisereformid dw 
                INNER JOIN m_department md ON md.department_code = dw.DeptID {$leftJoin}
                WHERE dw.is_deleted = 0 and m_parent_department_id IS NULL {$where} GROUP BY dw.DeptName ORDER BY dw.DeptName";

                $result = $this->db->getAll($sql);
                
                if($result){
                        //add All value
                        if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                                array_unshift($result,  array('value'=>'-1', 'label'=>'All')) ;
                        }
                }

                return $result;
        }

        public function getReform(){

                $where = '';
                $where2 = '';
                
                $department_id = $_POST["department_id"];

                if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"] && $_POST['sub_department_id'] != '-1')){
                        $department_id = $_POST["sub_department_id"];
                }

                if(isset($_POST['department_id']) && !empty($_POST['department_id']) && $_POST['department_id'] != '-1'){
                        $where .= " department_id = '{$department_id}'";
                        $where2 .= " WHERE department_id = '{$department_id}'";
                }

                if(isset($_POST['uploaded_dept_only']) && $_POST['uploaded_dept_only'] == 'not_completed'){

                        $sql = "SELECT DISTINCT Reform_Number as `value`, Reform_Number as label FROM firm_company_details
                        WHERE deleted = 0 AND errStatus = 'Valid' AND category='NA' AND Survey_Year = {$_POST["year"]} 
                        AND survey_comp_status NOT IN ('Completed','Not Interested') AND {$where} ORDER BY Reform_Number";  

                        $result = $this->db->getAll($sql);

                        if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                                //add All value
                                array_unshift($result,  array('value'=>'-1', 'label'=>'All')) ;
                        }

                        return $result;
                }
                // else if(isset($_POST['uploaded_dept_only']) && $_POST['uploaded_dept_only'] == 'all'){

                //         $sql = "SELECT DISTINCT Reform_Number as id, Reform_Number as text FROM firm_company_details
                //         WHERE deleted = 0 AND errStatus = 'Valid' AND category='NA' AND Survey_Year = {$_POST["year"]} 
                //         AND {$where} ORDER BY Reform_Number";  

                //         $result = $this->db->getAll($sql);

                //         if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                //                 //add All value
                //                 array_unshift($result,  array('id'=>'-1', 'text'=>'All')) ;
                //         }

                //         return $result;
                // }
                else{
                        $sql = "SELECT REPLACE(ReformNumber, '\'', '') AS reform_number FROM departmentwisereformid  {$where2}";  

                        $result = $this->db->getAll($sql);

                        //  non unique reform number
                        $non_unique_reform_numbers = array();

                        foreach($result  as $index => $item){
                                // convert string to array;
                                $reform_array = explode(',', $item['reform_number']);

                                foreach($reform_array as $key => $val){
                                        array_push($non_unique_reform_numbers , $val);
                                }
                        }

                        // non unique convert to  unique reform number 
                        $unique_array =  array_unique($non_unique_reform_numbers);

                        // sort ascending order
                        sort($unique_array);

                        // unique reform numbers
                        $main_result = array();

                        foreach($unique_array as $key => $val){
                                array_push($main_result , array('value'=>$val, 'label'=>$val));
                        }
                   
                        if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                                //add All value
                                array_unshift($main_result,  array('value'=>'-1', 'label'=>'All')) ;
                        }

                        return $main_result;
                }

        }

        public function getServiceCategory(){

                $where = '';
                
                $department_id = $_POST["department_id"];

                if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"])){
                        $department_id = $_POST["sub_department_id"];
                }

                if(isset($_POST['year']) && !empty($_POST['year'])){
                        $where .= " AND Survey_Year = {$_POST["year"]}";
                }

                if(isset($_POST['department_id']) && !empty($_POST['department_id'])){
                        $where .= " AND department_id = '{$department_id}'";
                }

                if(isset($_POST['reform']) && !empty($_POST['reform'])){
                        $where .= " AND Reform_Number = {$_POST["reform"]}";
                }

                if(isset($_POST['reform_number']) && !empty($_POST['reform_number'])){
                        $where .= " AND Reform_Number = {$_POST["reform_number"]}";
                }

                $sql = "SELECT DISTINCT Service_Used as `value` , Service_Used as label FROM firm_company_details
                WHERE deleted = 0 AND errStatus = 'Valid' AND category='NA' AND survey_comp_status NOT IN ('Completed','Not Interested') $where "; 

                return $this->db->getAll($sql);
        }

        public function getDistrict(){

                $where = '';

                $department_id = $_POST["department_id"];

                if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"])){
                        $department_id = $_POST["sub_department_id"];
                }

                if(isset($_POST['year']) && !empty($_POST['year'])){
                        $where .= " AND Survey_Year = {$_POST["year"]}";
                }

                if(isset($_POST['department_id']) && !empty($_POST['department_id'])){
                        $where .= " AND department_id = '{$department_id}'";
                }

                if(isset($_POST['reform']) && !empty($_POST['reform'])){
                        $where .= " AND Reform_Number = {$_POST["reform"]}";
                }

                if(isset($_POST['service_cate']) && !empty($_POST['service_cate'])){
                        $service = addslashes($_POST["service_cate"]);
                        $where .= " AND Service_Used = '{$service}'";
                }

                $sql = " SELECT DISTINCT District as `value` , District as label 
                FROM firm_company_details
                WHERE deleted =0 AND survey_comp_status !='Completed' 
                AND errStatus = 'Valid'  AND category='NA' 
                AND survey_comp_status NOT IN ('Completed','Not Interested') $where";

                return $this->db->getAll($sql);
        }

        public function getMonth(){

                $where = '';

                $department_id = $_POST["department_id"];

                if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"] && $_POST['sub_department_id'] != '-1')){
                        $department_id = $_POST["sub_department_id"];
                }

                if(isset($_POST['year']) && !empty($_POST['year'])){
                        $where .= " AND Survey_Year = {$_POST["year"]}";
                }

                if(isset($_POST['department_id']) && !empty($_POST['department_id']) && $_POST['department_id'] != '-1'){
                        $where .= " AND department_id = '{$department_id}'";
                }

                if(isset($_POST['reform']) && !empty($_POST['reform'])){
                  
                        if(is_array($_POST['reform']) && in_array('-1' , $_POST['reform']) == false){
                                $reform = implode(', ', $_POST['reform']);
                                $where .= " AND Reform_Number  IN ($reform)";
                        }
                        else if(is_array($_POST['reform']) == false){
                            $where .= " AND Reform_Number = {$_POST["reform"]}";
                        }

                }

                if(isset($_POST['service_cate']) && !empty($_POST['service_cate'])){
                        $service = addslashes($_POST["service_cate"]);
                        $where .= " AND Service_Used = '{$service}'";
                }

                if(isset($_POST['district']) && !empty($_POST['district'])){
                        $where .= " AND District = '{$_POST["district"]}'";
                }

                if(isset($_POST['uploaded_dept_only']) && $_POST['uploaded_dept_only'] == 'not_completed'){
                        $where .= " AND survey_comp_status NOT IN ('Completed','Not Interested')";
                }

                $sql = "SELECT DISTINCT `Month` as `value` , REPLACE(`Month`, '-','-20')  as label  FROM firm_company_details  
                WHERE deleted =0 AND errStatus = 'Valid' AND category='NA' $where ORDER BY Survey_Date DESC";

                $result = $this->db->getAll($sql);

                if(isset($_POST['all_value']) && $_POST['all_value'] == true){
                      //add All value
                      array_unshift($result,  array('value'=>'-1', 'label'=>'All')) ;
                }

                return $result;
        }

        public function getMonthRange(){
                $where = '';

                if(isset($_POST['year']) && !empty($_POST['year'])){
                        $where .= " AND Survey_Year = {$_POST["year"]}";
                }

                $sql = "SELECT MIN(Survey_Date) AS min_date, MAX(Survey_Date) AS max_date FROM firm_company_details  
                WHERE deleted = 0 AND errStatus = 'Valid' AND flag NOT IN (2) AND category='NA' {$where} ORDER BY Survey_Date DESC";

                return $this->db->getRow($sql);
        }

        public function getSurveyList(){

                $department_id = $_POST["department_id"];

                if(isset($_POST["sub_department_id"]) && !empty($_POST["sub_department_id"] && $_POST['sub_department_id'] != '-1')){
                        $department_id = $_POST["sub_department_id"];
                }

                $service = addslashes($_POST["service_cate"]);

                $sql = "SELECT DISTINCT(`firm_comp_id`), `Name_of_Firm`, Contact_Person, Mobile_Numer 
                FROM `firm_company_details` 
                WHERE `deleted` = 0  AND `category`='NA'
                AND errStatus = 'Valid'  AND `survey_comp_status` NOT IN ('Completed','Not Interested') 
                AND `department_id` ='{$department_id}'  AND `Reform_Number`='{$_POST["reform"]}'  
                AND `Service_Used`='{$service}'  AND `District`='{$_POST["district"]}' 
                AND `Month`='{$_POST["survey_month"]}' AND Survey_Year = {$_POST["year"]} 
                ORDER BY `Name_of_Firm`" ;

                return $this->db->getAll($sql);
        }

        public function getSurveyMemberDetails(){
                
                $sql ="SELECT Name_of_Firm, Contact_Person, Mobile_Numer, category, Contact_Address, E_mail,
                DATE_FORMAT(Date_of_Final_Approval, '%d-%b-%Y') AS Date_of_Final_Approval,
                department_id, Department, Reform_Number, Service_Used,
                Reform_Number, firm_comp_id, Service_Used, District,
                Month, pan_number FROM firm_company_details
                WHERE survey_comp_status !='Completed' AND firm_comp_id ='{$_POST['id']}' ";

                return $this->db->getRow($sql);
        }

        public function getsurveyID(){

                date_default_timezone_set('Asia/Kolkata');

                $userID = ''; $surveyID= ""; $login_status="In valid";

                if(isset($this->userID)){
                        
                        $sql="SELECT * FROM `user_login` WHERE `userID`='".$this->userID."' AND `login_key`='".$this->loginKey."'";
                        $query = $this->db->getRow($sql);
                        
                        if($query){
                                $userID=$this->userID;
                                $login_status="valid";
                                $surveyID=$userID."_".date('YmdHis');
                        }
                }

                $result = array('userID'=>$userID, 'login_status'=>$login_status,'surveyID'=>$surveyID);

                return  $result;
        }

        public function saveSurvey(){

                $data = $_POST['data'];

                if(isset($data['qb1'])){
                    $data['qb1'] = implode(',', $data['qb1']);
                }

                if(isset($data['qb2'])){
                    $data['qb2'] = implode(',', $data['qb2']);
                }
                
                $rs = $this->general->saveData('survey_response', 'id', $data);
                  
                if($rs){
                        $done_survey_date = date('Y-m-d h:i:s');

                        $updateData = array('firm_comp_id' => $data['firm_company'], 
                        'done_by' => $data['surveyID'], 
                        'done_survey_date' => $done_survey_date,
                        'survey_comp_status'=>$data['survey_status']);
        
                        $rs2 = $this->general->saveData('firm_company_details', 'firm_comp_id', $updateData);

                        if($rs2){
                                $jsonData = json_encode($data, true);

                                $_sql = "SELECT id FROM firm_company_details WHERE firm_comp_id = '{$data['firm_company']}'";
               
                                $dep_id =  $this->db->getAll($_sql);   

                                $sql = "INSERT INTO survey_response_temp ( firm_company_details_id, survey_response_id, response_json ) VALUES ( '{$dep_id[0]["id"]}', '{$rs}', '$jsonData') ";

                                $result = $this->db->Execute($sql);
                                  
                                if($result){
                                        return $this->db->Insert_ID($sql);
                                }
                        }
                
                }
                else{
                        return $rs;
                }
        
        }
}