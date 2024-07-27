<?php
class Reform{

    public function __construct($conn, $general) {
        global $m_year, $allow_month;
        $this->db = $conn;
        $this->general = $general;
        $this->cur_year = $m_year;
        $this->allow_month = $allow_month;
        $this->userID = $_POST['deviceID'];
    }

    public function getFormData(){
   
        $sql = "SELECT * FROM firm_company_details WHERE id = {$_POST['id']}";

        $result = $this->db->getAll($sql);
          
        return $result;
    }

    public function getReformList(){
   
        $ReformNumberSql = "SELECT ReformNumber FROM departmentwisereformid DW
        INNER JOIN  user_login UL ON UL.DeptID = DW.DeptID WHERE is_deleted = 0 AND UL.userID = '{$this->userID}' AND m_year =  {$this->cur_year}";
        $ReformNumber = $this->db->getAll($ReformNumberSql);
        
        $value  = $ReformNumber[0]['ReformNumber'];
        $sql = "SELECT * FROM reform_list  WHERE  ReformNumber IN ($value) AND m_year =  {$this->cur_year}"; 
   
        return $this->db->getAll($sql);
    }

    public function getMonthCount(){

        $filter = $_POST["filter"];

        if ($filter["Survey_Year"] == $this->cur_year) {
            $sql = "SELECT MONTH(meses.m_month) AS month_number, 
                              date_format(meses.m_month,'%b') AS survey_month,
                COUNT(fcd.id) AS survey_count, 
                (CASE
            WHEN DATE_FORMAT(meses.m_month, '%Y-%m') > DATE_FORMAT(CURDATE(), '%Y-%m') THEN 'secondary'
            WHEN COUNT(fcd.id) > 0 THEN (CASE
            WHEN  COUNT( CASE WHEN fcd.errStatus = 'Invalid' THEN fcd.id END) > 0 THEN 'warning'
            ELSE 'success'
        END)
            ELSE 'danger'
        END) AS survey_color,
                (CASE 
                    WHEN COUNT(fcd.id) > 0 THEN 'enabled' 
                    ELSE 'disabled' 
                END) AS survey_status ,
                case when month(meses.m_month) = MONTH(CURDATE()) then '1' ELSE '0' END AS select_survey
                FROM (";
            $c = 0;
            foreach ($this->allow_month as $_month) {
                if ($c == 0) {
                    $sql .= " SELECT '{$this->cur_year}-{$_month}-01' AS m_month ";
                    $c++;
                    continue;
                }
                $sql .= " UNION SELECT '{$this->cur_year}-{$_month}-01' AS m_month ";
            }
    
            $sql .= ") AS meses      
                    LEFT JOIN firm_company_details fcd ON MONTH(meses.m_month) = fcd.Survey_Month AND fcd.deleted = 0 AND flag NOT IN (2) AND fcd.Survey_Year = {$this->cur_year} AND userlogin_id = '{$this->userID}'
                    GROUP BY month_number";

            $_res = $this->db->getAll($sql);
  
        } 
        else {
        $sql = "SELECT MONTH(meses.m_month) AS month_number ,DATE_FORMAT(meses.m_month, '%b') AS survey_month,
        COUNT(fcd.id) AS survey_count, (case when COUNT(fcd.id) > 0 then 'success' ELSE 'danger' END )AS survey_color,
            (case when COUNT(fcd.id) > 0 then 'enabled' ELSE 'disabled' END )AS survey_status ,0 AS select_survey
            FROM(SELECT '{$filter['Survey_Year']}-1-1' AS m_month   
            UNION SELECT '{$filter['Survey_Year']}-2-1' AS m_month                    
            UNION SELECT '{$filter['Survey_Year']}-3-1' AS m_month
            UNION SELECT '{$filter['Survey_Year']}-4-1' AS m_month
            UNION SELECT '{$filter['Survey_Year']}-5-1' AS m_month
            UNION SELECT '{$filter['Survey_Year']}-6-1' AS m_month
            UNION SELECT '{$filter['Survey_Year']}-7-1' AS m_month
            UNION SELECT '{$filter['Survey_Year']}-8-1' AS m_month
            UNION SELECT '{$filter['Survey_Year']}-9-1' AS m_month
            UNION SELECT '{$filter['Survey_Year']}-10-1' AS m_month
            UNION SELECT '{$filter['Survey_Year']}-11-1' AS m_month 
            UNION SELECT '{$filter['Survey_Year']}-12-1' AS m_month  ) as meses 
            LEFT JOIN firm_company_details fcd ON MONTH(meses.m_month) = fcd.Survey_Month  AND Year(meses.m_month) = fcd.Survey_Year
            WHERE fcd.deleted = 0 AND flag NOT IN (2) AND fcd.userlogin_id = '{$this->userID}'
            GROUP BY meses.m_month ORDER BY month_number";

            $_res = $this->db->getAll($sql);

          
            if ($_res) {
                $_res[0]["select_survey"] = '1';
            }
        }

        
    
        return $_res;

    }

    public function uploadStatus(){
        
        $filter = $_POST["filter"];

        $sql = "SELECT d1.ReformNumber FROM user_login ul INNER JOIN departmentwisereformid d1 ON ul.DeptID = d1.DeptID 
        WHERE  d1.m_year = '{$filter['Survey_Year']}' AND  ul.userID = '{$this->userID}' ";

        $result = $this->db->getAll($sql);

        $reform_no = $result[0]['ReformNumber'];

        $main_sql = "SELECT rl.dept_ReformNumber, COUNT(fcd.id) AS data_count FROM reform_list rl
        LEFT JOIN firm_company_details fcd ON rl.ReformNumber = fcd.Reform_Number AND Survey_month = '{$filter['Survey_Month']}' 
        AND flag NOT IN (2) AND fcd.Survey_Year = '{$filter['Survey_Year']}' AND fcd.deleted = 0  AND userlogin_id = '{$this->userID}'
        WHERE rl.ReformNumber IN ($reform_no)
        AND rl.m_year = '{$filter['Survey_Year']}'  GROUP BY rl.dept_ReformNumber ORDER BY rl.dept_ReformNumber ";
     
        $main_result = $this->db->getAll($main_sql);

        return $main_result;
    }

    public function getCompanyList(){

        $filter = $_POST["filter"];

        $where = '';

        $main_where = '';

        if(isset($filter["Survey_Year"]) && $filter["Survey_Year"] !== ""){
            $where .= " AND Survey_Year = '{$filter['Survey_Year']}'";
        }

        if(isset($filter["Survey_Month"]) && $filter["Survey_Month"] !== ""){
            $where .= " AND Survey_Month = '{$filter['Survey_Month']}'";
        } 

        if(isset($filter["errStatus"]) && $filter["errStatus"] !== ""){
            $main_where .= " AND errStatus = '{$filter['errStatus']}'";
        } 

        $where.= " AND userlogin_id = '{$this->userID}'";

        $sql_count = "SELECT COUNT(*) FROM firm_company_details  WHERE deleted = 0  $where  AND flag NOT IN (2) ORDER BY upload_time DESC"; 

        $valid_sql_count = "SELECT COUNT(*) FROM firm_company_details  WHERE deleted = 0  $where  AND flag NOT IN (2) AND errstatus = 'Valid' ORDER BY upload_time DESC";
        $Invalid_sql_count = "SELECT COUNT(*) FROM firm_company_details  WHERE deleted = 0  $where  AND flag NOT IN (2) AND errstatus = 'Invalid' ORDER BY upload_time DESC";
 
        $sql = "SELECT *,CASE WHEN Survey_Year = '2022' THEN Reform_Number ELSE dept_ReformNumber END AS new_ReformNumber  
        FROM firm_company_details  WHERE deleted = 0  $where  $main_where AND flag NOT IN (2) ORDER BY upload_time DESC";  
    
        $result = $this->db->getAll($sql);

        $result_count = $this->db->getOne($sql_count);
        $valid_count = $this->db->getOne($valid_sql_count);
        $invalid_count = $this->db->getOne($Invalid_sql_count);

        $upload_status = $this->uploadStatus();

        return array(
            "data"=>$result,
            'recordsTotal' => $result_count,
            'valid'=>$valid_count,
            'invalid'=>$invalid_count,
            'upload_status' => $upload_status
        );

    }

    public function getDeptReformNumber(){
        
        $ReformNumberSql = "SELECT ReformNumber FROM departmentwisereformid DW INNER JOIN  user_login UL ON UL.DeptID = DW.DeptID WHERE UL.userID = '{$this->userID}' AND m_year = {$this->cur_year}";
        $ReformNumber = $this->db->getAll($ReformNumberSql);
        $value  = $ReformNumber[0]['ReformNumber'];
        $sql = "SELECT dept_ReformNumber as id ,  dept_ReformNumber as text FROM reform_list  WHERE  ReformNumber IN ($value) AND m_year =  {$this->cur_year}"; 

        return $this->db->getAll($sql);
    }

    public function getDistrictMaster(){

        $dist_array = array('Ariyalur','Chengalpet','Chennai','Coimbatore','Cuddalore','Dharmapuri','Dindigul','Erode','Kallakurichi','Kancheepuram','Kanniyakumari','Karur','Krishnagiri','Madurai','Mayiladuthurai','Nagapattinam','Namakkal','Perambalur','Pudukkottai','Ramanathapuram','Ranipet','Salem','Sivaganga','Tenkasi','Thanjavur','Nilgiris','Theni','Thoothukudi','Tiruchirappalli','Tirunelveli','Tirupathur','Tiruppur','Tiruvallur','Tiruvannamalai','Tiruvarur','Vellore','Viluppuram','Virudhunagar');
        $result = array();
        foreach($dist_array as $val){
            array_push($result, array('id'=>$val, 'text'=>$val));
        }

        return $result;
    }

    // check available service in master
    public function availableService($service){
        $sql = "SELECT * FROM  m_service WHERE is_deleted = 0 AND `service_name` = '{$service}' ";
        $result = $this->db->GetAll($sql);
        return $result;
    }

    public function saveReform(){

        $data = json_decode($_POST['data'], true);
        
        $sql = "SELECT DW.department_id, DW.DeptID, DW.DeptName FROM departmentwisereformid DW 
        INNER JOIN  user_login UL ON UL.DeptID = DW.DeptID WHERE UL.userID = '{$this->userID}' AND DW.m_year = {$this->cur_year}";

        $rs = $this->db->GetAll($sql);
       
        $data['DeptID'] = $rs[0]['DeptID'];
        $data['Department'] = $rs[0]['DeptName'];
        $data['department_id'] = $rs[0]['department_id'];

        $servertime = date("Y-m-d H:i:s");
        
        if(isset($data['S_No']) && !empty($data['S_No'])){
            $firm_comp_id = $data['S_No'].'_INS_'.$servertime;
        }
        else{
            $firm_comp_id = '1_INS_'.$servertime;
        }

        $data['firm_comp_id'] = $firm_comp_id ;
        $data['userlogin_id'] = $this->userID;
        $data['errStatus'] = 'Valid';
        $data['dataError'] = '';

        // service used trim
        $data['Service_Used'] = trim($data['Service_Used']);

        if(!empty($data['Service_Used'])){
            $isService =  $this->availableService($data['Service_Used']);
            if(!$isService){
                return  array('msg'=> "This service <b>'{$data['Service_Used']}'</b> not match in master"); 
            }
        }
        $sql = "SELECT ReformNumber FROM reform_list  WHERE  dept_ReformNumber = '{$data['dept_ReformNumber']}' AND m_year =  {$this->cur_year}"; 
        $RN = $this->db->GetAll($sql);

        $data['Reform_Number'] = $RN[0]['ReformNumber'];

        $month_year = explode("-",$data['Month']);

        $month = date_format(date_create($month_year[0]),"n");
        $Survey_Date = date_format(date_create($data['Month']),"Y-m-d");
        $year = intval($month_year[1]);

        $data['Month'] = date_format(date_create($data['Month']),"M-y");
        $data['Survey_Month'] = $month;
        $data['Survey_Year'] = $year;
        $data['Survey_Date'] = $Survey_Date;
        $data['date_of_application'] = date_format(date_create($data['date_of_application']),"Y-m-d");
        $data['Date_of_Final_Approval'] = date_format(date_create($data['Date_of_Final_Approval']),"Y-m-d");

        $data['ujslog'] = json_encode($data, true);

        $rs = $this->general->saveData('firm_company_details', 'id', $data);
        
        return $rs;
        
    }

    public function deleteReform(){
        $all = $_POST['id'];
        $all = json_decode($all, true);

        if(is_array($all)){
           $all = implode(',', $all);
        }

        $date_time = date("Y-m-d H:i:s");

        $sql = "UPDATE firm_company_details SET deleted = 10, deleted_time = '{$date_time}'  WHERE id in ($all)";
        
        $rs = $this->db->Execute($sql);

        return $rs;
        
    }
}