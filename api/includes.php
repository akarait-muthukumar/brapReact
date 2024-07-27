<?php
include_once("./config/db_connect.php");

include_once('libraries/jwt/jwt_token_generator.php');

include_once("./session.php");

include_once('class/general.php');

include_once('class/auth.php');

include_once('class/master.php');
include_once('class/dashboard.php');
include_once('class/interviewer_dashboard.php');
include_once('class/department_wise.php');
include_once('class/user_management.php');
include_once('class/survey.php');
include_once('class/report.php');
include_once('class/mis_status.php');
include_once('class/reform.php');
include_once('class/area_of_findings.php');
include_once('class/daily_call_report.php');
include_once('class/sample_size.php');
include_once('class/downloadexcel.php'); 
include_once('class/uploadExcel.php'); 


// global variables
global $m_year, $allow_month, $m_year_title, $md5;

// user password encrpt character
$md5 = 'tneodb';

$db = new DBConfig();
$conn = $db->dbConnection();

$general = new General($conn);

$auth = new Auth($conn, $general);

$master = new Master($conn, $general);

$dashboard = new Dashboard($conn);
$interviewerDashboard = new InterviewerDashboard($conn);
$departmentWise = new DepartmentWise($conn, $master);

$userManagement = new UserManagement($conn, $general);
$survey = new Survey($conn, $general);
$report = new Report($conn);
$reform = new Reform($conn, $general);
$area_of_findings = new AreaOfFindings($conn, $general);
$misStatus = new MISStatus($conn, $master);
$dailyCallReport = new DailyCallReport($conn);
$sampleSize = new SampleSize($conn, $general, $master);
$downloadexcel = new Downloadexcel($conn, $report, $dailyCallReport, $area_of_findings);
$uploadExcel = new UploadExcel($conn, $general);



