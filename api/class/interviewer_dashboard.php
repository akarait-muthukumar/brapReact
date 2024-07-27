<?php
class InterviewerDashboard{

    public function __construct($conn) {
        global $m_year;
        $this->db = $conn;
        $this->cur_year = $m_year;
        $this->userID = $_POST['deviceID'];
    }

    public function getDepartmentWiseList(){

        $where = '';

        if(!empty($_POST['survey_date'])){

            $where .= " AND DATE(fcd.done_survey_date) BETWEEN DATE('{$_POST['survey_date'][0]}') AND DATE('{$_POST['survey_date'][1]}')";
        }

        $userID = $this->userID.'%';

        $sql ="SELECT fcd.Department as department, 
        COUNT(CASE WHEN survey_comp_status = 'Completed' THEN  survey_comp_status END ) AS completed ,
        COUNT(CASE WHEN survey_comp_status = 'Not Interested' THEN  survey_comp_status END ) AS not_interested
        From  firm_company_details fcd 
        WHERE fcd.deleted = 0 AND fcd.done_by  LIKE '{$userID}' AND fcd.Survey_Year = {$this->cur_year} {$where}  GROUP BY fcd.Department";

        $result = $this->db->getAll($sql);

        $table = array();

        $completed = array_sum(array_column($result, 'completed'));
        $not_interested = array_sum(array_column($result, 'not_interested'));

        $table['completed'] = $completed;
        $table['not_interested'] = $not_interested;
        $table['data'] = $result;

	    return $table;
    }

    public function getDateWiseList(){

        $where = '';

        $userID = $this->userID.'%';

        $sql="SELECT DATE_FORMAT(fcd.done_survey_date , '%d-%b-%Y') AS survey_date, 
        COUNT(CASE WHEN survey_comp_status = 'Completed' THEN  survey_comp_status END ) AS completed ,
        COUNT(CASE WHEN survey_comp_status = 'Not Interested' THEN  survey_comp_status END ) AS not_interested
        From firm_company_details fcd WHERE fcd.deleted = 0 AND fcd.done_by  LIKE '{$userID}' AND fcd.Survey_Year = {$this->cur_year}
        GROUP BY DATE_FORMAT(fcd.done_survey_date , '%d-%b-%Y')  ORDER BY fcd.done_survey_date DESC";

        $result = $this->db->getAll($sql);

        $table = array();

        $completed = array_sum(array_column($result, 'completed'));
        $not_interested = array_sum(array_column($result, 'not_interested'));

        $table['completed'] = $completed;
        $table['not_interested'] = $not_interested;
        $table['data'] = $result;

        return $table;
	
    }

}