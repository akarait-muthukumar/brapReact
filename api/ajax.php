<?php

    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');

    date_default_timezone_set('Asia/Kolkata');

    if(isset($_POST['userDetails'])){
        $userDetails = json_decode(base64_decode($_POST['userDetails']), true);
        $_POST["deviceID"] = $userDetails['deviceID'];
        $_POST["loginKey"] = $userDetails['login_Key'];
        $_POST["updated_by"] = $userDetails['user_id'];
    }
 
    include 'includes.php';

    $res["error_code"] = 400;
    $res["data"] = array();

    // JWT code start
    $avoid_api_type = array("auth");
    $header_key = str_replace('-','_',strtoupper('X-eodb-Authorization'));
    $jwt_token = isset($_SERVER['HTTP_'.$header_key]) ? $_SERVER['HTTP_'.$header_key] : "";

    if(!in_array($_POST["type"], $avoid_api_type)){
        //Check the time when jwt_token is not empty 
        if(!empty($jwt_token)){
            if(!getStoredDataFromJwtToken($jwt_token)){
                $data = $auth->logout();
                if($data){
                    $res["error_code"] = 440;
                    echo json_encode($res, true);
                    exit();
                }
            }
        }
        else{
            $res["error_code"] = 440;
            echo json_encode($res, true);
            exit();
        }
    }
  
    if($_POST["type"] == 'auth'){
        $data = $auth->login();
        if ($data) {
            $res["error_code"] = 200;
            $res["data"] = $data;
            $res["message"] = 'Login successfully';
        }
        else{
            $res["message"] = 'User not found';
        }
    }
    else if (isset($_POST["type"]) && isset($_POST['userDetails'])) {

        // update activity
        $auth->updateActivity();
        $valid_key = $auth->isLoginKeyValid();

        if($valid_key){
            switch ($_POST["type"]) {
                // Master page
                case "getMasterDepartment" :
                    $data = $master->getMasterDepartment();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getDepartment" :
                    $data = $master->getDepartment();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getParentDepartment" :
                    $data = $master->getParentDepartment();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getSubDepartment" :
                    $data = $master->getSubDepartment();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveDepartment" :
                    $data = $master->saveDepartment();
                    $payload = json_decode($_POST['data'], true);
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                        if($payload['m_department_id'] != -1){
                            $res["message"] = 'Update Successfully';
                        }
                        else{
                            $res["message"] = 'Added Successfully';
                        }
                    }break;
                case "getMasterReformNumber" :
                    $data = $master->getMasterReformNumber();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getReformNumbers" :
                    $data = $master->getReformNumbers();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveReformNumber" :
                    $data = $master->saveReformNumber();
                    $payload = json_decode($_POST['data'], true);
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                        if($payload['m_reform_id'] != -1){
                            $res["message"] = 'Update Successfully';
                        }
                        else{
                            $res["message"] = 'Added Successfully';
                        }
                    }break;
                case "getDeptWiseReformNo" :
                    $data = $master->getDeptWiseReformNo();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "checkDeptAlreadyhaveReformNo" :
                    $data = $master->checkDeptAlreadyhaveReformNo();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveDeptWiseReformNo" :
                    $data = $master->saveDeptWiseReformNo();
                    $payload = json_decode($_POST['data'], true);
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                        if($payload['department_id'] != -1){
                            $res["message"] = 'Update Successfully';
                        }
                        else{
                            $res["message"] = 'Added Successfully';
                        }
                    }break;
   
                case "getMasterYear" :
                    $data = $master->getMasterYear();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "activeYear" :
                    $data = $master->activeYear();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                case "saveYear" :
                    $data = $master->saveYear();
                    $payload = json_decode($_POST['data'], true);
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                        if($payload['m_year_id'] != -1){
                            $res["message"] = 'Update Successfully';
                        }
                        else{
                            $res["message"] = 'Added Successfully';
                        }
                    }break;
                
                case "saveGroup" :
                    $data = $master->saveGroup();
                    $payload = json_decode($_POST['data'], true);
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                        if($payload['m_group_id'] != -1){
                            $res["message"] = 'Update Successfully';
                        }
                        else{
                            $res["message"] = 'Added Successfully';
                        }
                    }break;

                case "getMasterGroup" :
                    $data = $master->getMasterGroup();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                case "getGroup" :
                    $data = $master->getGroup();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                case "getServices" :
                    $data = $master->getServices();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                case "getServiceList" :
                    $data = $master->getServiceList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveService" :
                    $data = $master->saveService();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;


                
                // survey page 
                case "getInterviewerList" :
                    $data = $survey->getInterviewerList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getDepartmentList" :
                    $data = $survey->getDepartmentList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getReform" :
                    $data = $survey->getReform();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getServiceCategory" :
                    $data = $survey->getServiceCategory();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getDistrict" :
                    $data = $survey->getDistrict();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getMonth" :
                    $data = $survey->getMonth();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getMonthRange" :
                    $data = $survey->getMonthRange();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getSurveyList" :
                    $data = $survey->getSurveyList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getSurveyMemberDetails" :
                    $data = $survey->getSurveyMemberDetails();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getsurveyID" :
                    $data = $survey->getsurveyID();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveSurvey" :
                    $data = $survey->saveSurvey();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                // MIS Status
                case "getMISStatus" :
                    $data = $misStatus->getMISStatus();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getMISStatusExcel" :
                    return $downloadexcel->getMISStatusExcel();
                    break;
        
                //Report
                case "getReportList" :
                    $data = $report->getReportList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getReportExcel" :
                    $data = $downloadexcel->getReportExcel();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                //Reform 
                case "getReformList" :
                    $data = $reform->getReformList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getYear" :
                    $data = $general->getYear();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getCompanyList" :
                    $data = $reform->getCompanyList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getDeptReformNumber" :
                    $data = $reform->getDeptReformNumber();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getDistrictMaster" :
                    $data = $reform->getDistrictMaster();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveReform" :
                    $data = $reform->saveReform();
                    if ($data) {
                        if(is_array($data)){
                            $res["msg"] = $data['msg'];
                        }
                        else{
                            $res["error_code"] = 200;
                            $res["data"] = $data;
                        }
                    }
                    else{
                        $res["msg"] = "File not save";
                    }
                    break;
                case "getMonthCount" :
                    $data = $reform->getMonthCount();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "deleteReform" :
                    $data = $reform->deleteReform();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getFormData" :
                    $data = $reform->getFormData();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                // Upload Excel
                case "uploadReformExcel" :
                    $data = $uploadExcel->uploadReformExcel();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                // Dashboard
                case "getDepartmentCount" :
                    $data = $dashboard->getDepartmentCount();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getCompletedSurveyCount" :
                    $data = $dashboard->getCompletedSurveyCount();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getDepartmentPeformance" :
                    $data = $dashboard->getDepartmentPeformance();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                
                // Department Wise Report
                case "departmentWiseReport" :
                    $data = $departmentWise->departmentWiseReport();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                case "getDepartmentId" :
                    $data = $departmentWise->getDepartmentId();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                // User Management
                case "getUserType" :
                    $data = $userManagement->getUserType();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getUserList" :
                    $data = $userManagement->getUserList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveUser" :
                    $data = $userManagement->saveUser();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "resetLoginKey" :
                    $data = $userManagement->resetLoginKey();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                // Daily Call Report
                case "getDailyCallReport" :
                    $data = $dailyCallReport->getDailyCallReport();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getdailyreportExcel" :
                    return $downloadexcel->getdailyreportExcel();
                    break;

                // Sample Size
                case "getSampleSizeList" :
                    $data = $sampleSize->getSampleSizeList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveSampleSize" :
                    $data = $sampleSize->saveSampleSize();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                
                // interviewerDashboard
                case "getDepartmentWiseList" :
                    $data = $interviewerDashboard->getDepartmentWiseList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                case "getDateWiseList" :
                    $data = $interviewerDashboard->getDateWiseList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                // area_of_findings
                case "getMonthAF" :
                    $data = $area_of_findings->getMonthAF();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getDepartmentAF" :
                    $data = $area_of_findings->getDepartmentAF();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getReformNumberAF" :
                    $data = $area_of_findings->getReformNumberAF();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getServiceCategoryAF" :
                    $data = $area_of_findings->getServiceCategoryAF();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getAreaFindingList" :
                    $data = $area_of_findings->getAreaFindingList();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "getAreaOfindingExcel" :
                    $data = $downloadexcel->getAreaOfindingExcel();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;
                case "saveAF" :
                    $data = $area_of_findings->saveAF();
                    $payload = json_decode($_POST['data'], true);
                    if ($data) {
                        if(is_array($data)){
                            $res["message"] = $data['msg'];
                        }
                        else{
                            $res["error_code"] = 200;
                            $res["data"] = $data;
                            if($payload['area_of_findings_id'] != -1){
                                $res["message"] = 'Update Successfully';
                            }
                            else{
                                $res["message"] = 'Added Successfully';
                            }
                        }


                    }break;

                // Logout
                case "logout" :
                    $data = $auth->logout();
                    if ($data) {
                        $res["error_code"] = 200;
                        $res["data"] = $data;
                    }break;

                default :
                    $res["error_mes"] = "Invalid Authentication Please Contact Admin.";
                break;
            }
        }
        else{
            $data = $auth->logout();
            if ($data) {
                $res["error_code"] = 200;
                $res["data"] = $data;
                $res["logout"] = true;
            }
        }
    }

    echo json_encode($res, true);

?>