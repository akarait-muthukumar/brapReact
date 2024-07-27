<?php
class UploadExcel{

    public function __construct($conn, $general) {
        global $m_year;
        $this->db = $conn;
        $this->general = $general;

        $this->userID = $_POST['deviceID'];
        $this->cur_year = $m_year;
        $this->month = '';

        $this->reform_no = '';

        $this->fileMimes = array(
            'text/x-comma-separated-values',
            'text/comma-separated-values',
            'application/octet-stream',
            'application/vnd.ms-excel',
            'application/x-csv',
            'text/x-csv',
            'text/csv',
            'application/csv',
            'application/excel',
            'application/vnd.msexcel',
            'text/plain',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' //Added by GG to allow xlsx
        );


        $this->headers = array('S_No', 'dept_ReformNumber', 'Service_Used', 'date_of_application', 'Date_of_Final_Approval',
            "Name_of_Firm" , "pan_number" , "Contact_Address" , "firm_city" , "District" , "Pincode" , 
            "Contact_Person" , "Designation" , "E_mail", "Mobile_Numer" , "firm_additionalMobile"
        );

        $this->require_fields = array('dept_ReformNumber', 'Service_Used', 'date_of_application', 'Date_of_Final_Approval',
            "Name_of_Firm" , "Contact_Address" , "firm_city" , "District" , "Pincode" ,  "Contact_Person", "E_mail", "Mobile_Numer"
        );

        $this->col_match = array('S.No', 'Reform No', 'Name Of Service', 'Date of Application', 'Date of Approval',
            "Company/Individual Name" , "PAN of the Company" , "Address" , "City" , "District" , "Pincode" , 
            "Contact person" , "Designation" , "Email", "Mobile Number" , "Landline Number"
        );

        $this->upload_count = 0;

    }

    public function checkDeptReformNumber($data){

        $sql = "SELECT dept_ReformNumber FROM reform_list  WHERE  ReformNumber IN ({$this->reform_no}) AND m_year = '{$this->cur_year}' "; 
        $result = $this->db->getAll($sql);

        $reform_array = array_map(function ($item) {
            return $item["dept_ReformNumber"];
        }, $result);

        $rs = in_array($data , $reform_array);

        return $rs;
    }

    public function checkDepartment(){
        
        $sql = "SELECT DW.department_id, DW.DeptID, DW.DeptName, ReformNumber FROM departmentwisereformid DW 
        INNER JOIN  user_login UL ON UL.DeptID = DW.DeptID
        WHERE UL.userID = '{$this->userID}' AND m_year =  '{$this->cur_year}'";

        $result = $this->db->GetAll($sql);

        $this->reform_no = $result[0]['ReformNumber'];
        
        return $result;
    }

    public function columnMisMatch($data){

        $matching_count = 0;

        for ($i = 0; $i < count($this->col_match); $i++) {

            if (isset($data[0][$i]) && $this->col_match[$i] === $data[0][$i]) {

                $matching_count++;

            }
        }
        
        return $matching_count;
    }

    public function dateFormat($str){
        if(!empty($str)){
            $x = preg_split("/[ -\/:-@\[-\`{-~]/", $str);
            if(checkdate($x[1], $x[0], $x[2])){
                if(strlen($x[2]) == 2){
                    $dates = DateTime::createFromFormat('y', $x[2]);
                    $x[2] = $dates->format('Y');
                }
                return $x[2].'-'.$x[1].'-'.$x[0];
            }
            else{
                return false;
            }
        }
        else{
            return $str;
        }
    }

    public function dataError($data){
        $dataError = '';
        foreach($this->require_fields as $key => $val){

            if (empty($data[$val])) {
                $dataError .= '<b>'.$val.'</b> is Blank, ';
            }
            else{
                // pincode
                if($val == 'Pincode'){
                    if(substr($data[$val], 0, 1) != '6' || is_numeric($data[$val]) != 1 || strlen($data[$val]) != 6){
                        $dataError .= 'Invalid <b>Pincode</b>, ';
                    }
                }

                // Too short error
                if($val == 'Contact_Person' || $val == 'Contact_Address' || $val == 'firm_city' || $val == 'Service_Used'){
                    if (strlen($data[$val]) < 2) {
                        $dataError .= '<b>'.$val.'</b> is too short, ';
                    }
                }

                //District
                if($val == 'District'){
                    $firm_district_array = array('Ariyalur', 'Chengalpet', 'Chennai', 'Coimbatore', 'Cuddalore', 'Dharmapuri', 'Dindigul', 'Erode', 'Kallakurichi', 'Kancheepuram', 'Kanniyakumari', 'Karur', 'Krishnagiri', 'Madurai', 'Mayiladuthurai', 'Nagapattinam', 'Namakkal', 'Perambalur', 'Pudukkottai', 'Ramanathapuram', 'Ranipet', 'Salem', 'Sivaganga', 'Tenkasi', 'Thanjavur', 'Nilgiris', 'Theni', 'Thoothukudi', 'Tiruchirappalli', 'Tirunelveli', 'Tirupathur', 'Tiruppur', 'Tiruvallur', 'Tiruvannamalai', 'Tiruvarur', 'Vellore', 'Viluppuram', 'Virudhunagar');
                    if (!in_array(trim($data[$val]), $firm_district_array)) {
                        $dataError .= 'The <b>District</b> is invalid, ';
                    }
                }

                //Email
                if($val == 'E_mail'){
                    if (!filter_var($data[$val], FILTER_VALIDATE_EMAIL)) {
                        $dataError .=  'Invalid <b>email</b>, ';
                    }
                }

                //Mobile Number
                if($val == 'Mobile_Numer'){
                    $pattern = "/^[6-9]{1}[0-9]{9}$/";
                    if (!preg_match($pattern, $data[$val])) {
                        $dataError .=  'Invalid <b>Mobile Number</b>, ';
                    }
                    else if (strlen($data[$val]) != 10) {
                        $dataError .=  '<b>Mobile Number</b> 10 digit required, ';
                    }
                }


                //date_of_application
                if($val == 'date_of_application'){
                   if(!empty($data['date_of_application'])) {
                       $a = explode("-",$this->month);
                       $b = explode("-",$data[$val]);
                       $month = date_format(date_create($a[0]),"n");

                       if($month < $b[1]){
                            $dataError .=  '<b>Date of application</b> cannot be previous month from Survey Month., ';
                       }
                    }
                }

                //Date_of_Final_Approval
                if($val == 'Date_of_Final_Approval'){
                   if(!empty($data['date_of_application'])) {

                       if($data['date_of_application'] >  $data[$val]){
                            $dataError .=  '<b>Date of approval</b> cannot be previous date from date of application., ';
                       }

                       $a = explode("-",$this->month);
                       $b = explode("-",$data[$val]);
                       $month = date_format(date_create($a[0]),"n");

                       if($month != $b[1]){
                            $dataError .=  '<b>Date of approval</b> cannot be previous date from Survey Month., ';
                       }
                    }
                }
            }
        }

        // Extra validate ..........

        //Pan validation
        if (!empty($data['pan_number'])) {
            $pan_pattern = "/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/";
            if (!preg_match($pan_pattern, $data['pan_number'])) {
                $dataError .= 'Invalid <b>PAN Number</b>, ';
            }
        }

        //Mobile Number
        if(!empty($data['firm_additionalMobile'])){
            $pattern = "/^[6-9]{1}[0-9]{9}$/";
            if (!preg_match($pattern, $data['firm_additionalMobile'])) {
                $dataError .=  'Invalid <b>Landline Number</b>, ';
            }
            else if (strlen($data['firm_additionalMobile']) != 10) {
                $dataError .=  '<b>Landline Number</b> 10 digit required, ';
            }
        }

        return $dataError;

    }


    // check available service in master
    public function availableService($service){
        $sql = "SELECT * FROM  m_service WHERE is_deleted = 0 AND `service_name` = '{$service}' ";

        $result = $this->db->GetAll($sql);
        
        return $result;

    }

    public function uploadReformExcel() {

        $result = array('error'=>1,'message'=>'', "count"=>0);

        $this->month = $_POST['Survey_Month'];

        if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $this->fileMimes)) {

            // User eligible test
                $dept_result =  $this->checkDepartment();
                if($dept_result == null){
                    $result['message'] = 'You are Not Eligible this Year Survey';
                    return  $result;
                }

            // Get Data from phpexcel ... Its take some times
                try{
                    
                    $excel_data = getListFromExcel($_FILES['file']['tmp_name']);

                }catch(Exception $e){
                    if($e instanceof PHPExcel_Calculation_Exception){
                        $error = $e->getMessage();

                        //string break
                        $str = strpbrk($error, '!');

                        // Filter the Numbers from String
                        $excel_row_no = (int)filter_var($error, FILTER_SANITIZE_NUMBER_INT);

                        $result['message'] = "Unexpected value in ".$str[1]." column at line number ".$excel_row_no.". Please check and upload.";
                        return  $result;
                    }
                    $result['message'] = $e->getMessage();
                    return  $result;
                }
                
            // Column mis-matching test
                $mis_match_result =  $this->columnMisMatch($excel_data);
                if($mis_match_result != 16){
                    $result['message'] = 'Column mismatching, please check with the template downloaded';
                    return  $result;
                }

            //  We should remove headers from Excel data array;
            array_shift($excel_data);

            // Insert Data into database
            foreach($excel_data as $key => $val) {

                $row = array();

                foreach($this->headers as $i => $name){
                    $row[$name] = trim($val[$i]);
                }

                // Convert the data into correct format

                $row['date_of_application'] = $this->dateFormat($row['date_of_application']);
                $row['Date_of_Final_Approval'] = $this->dateFormat($row['Date_of_Final_Approval']);

                // service used trim
                $row['Service_Used'] = trim($row['Service_Used']);

                if(!empty($row['Service_Used'])){
                   $isService =  $this->availableService($row['Service_Used']);
                   if(!$isService){
                        $result['error'] = 1;
                        $result['message'] = "This service <b>'{$row['Service_Used']}'</b> not match in master";
                        break;
                   }
                }

                // Set Error 
                $row['dataError'] = $this->dataError($row);
                 
              
                // Get and set other extra fields data
                $row['DeptID'] = $dept_result[0]['DeptID'];
                $row['Department'] = $dept_result[0]['DeptName'];
                $row['department_id'] = $dept_result[0]['department_id'];


                if(!empty($row['dept_ReformNumber'])){

                    $dept_reform_valid = $this->checkDeptReformNumber($row['dept_ReformNumber']);

                    if($dept_reform_valid){
                        $RN_sql = "SELECT ReformNumber FROM reform_list  WHERE  dept_ReformNumber = '{$row['dept_ReformNumber']}' AND m_year =  {$this->cur_year}"; 
                        $RN = $this->db->GetAll($RN_sql);
    
                        $row['Reform_Number'] = $RN[0]['ReformNumber'];
                    }
                 
                }

                $servertime = date("Y-m-d H:i:s");
                $firm_comp_id = $key.'_'.$key.'_'.$servertime;
                $row['firm_comp_id'] = $firm_comp_id ;
                $row['userlogin_id'] = $this->userID;
        
                $month_year = explode("-",$this->month);
        
                $month = date_format(date_create($month_year[0]),"n");
                $Survey_Date = date_format(date_create($this->month),"Y-m-d");
                $year = intval($month_year[1]);
        
                $row['Month'] = date_format(date_create($this->month),"M-y");
                $row['Survey_Month'] = $month;
                $row['Survey_Year'] = $year;
                $row['Survey_Date'] = $Survey_Date;

                ($row['dataError'] != '') ? $row['errStatus'] = 'Invalid' : $row['errStatus'] = 'Valid' ;

                $row['ujslog'] = json_encode($row, true);

                $rs = $this->general->saveData('firm_company_details', 'id', $row);

                if($rs){
                    $this->upload_count = intval($this->upload_count) + 1;
                    $result['error'] = 0;
                    $result['message'] = 'Upload Successfully';
                }else{
                    $result['error'] = 1;
                    $result['message'] = 'Not Upload';
                    break;
                }
               
            }

            $result['count'] = $this->upload_count;

            return $result;

        }
        else{
            $result['message'] = 'I am unable to read your data file 📖📖📖..., can you please check that it is in CSV format?';
            return  $result;
        }

        $user_result = $this->db->getAll($user_sql);
        
    }

}
