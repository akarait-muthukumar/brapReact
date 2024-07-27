<?php

class Downloadexcel {
    
    public function __construct($conn, $report, $dailyCallReport, $area_of_findings) {
        $this->db = $conn;
        $this->report = $report;
        $this->dailyCallReport = $dailyCallReport;
        $this->area_of_findings = $area_of_findings;
        $this->excel_title = 'excel';
        $this->objExcel = new PHPExcel();
        $this->headerstyle = array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => '70ad47'
            ),
        );
        $this->styleArray = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'color' => array('rgb' => 'DDDDDD')
            ),
            'borders' => array(
            )
        );
    }

    public function getdailyreportExcel() {

        $header_data = $this->dailyCallReport->getDailyCallReport();

        if ($header_data) {
            $header = (array_map(function ($str) {
                        return ucfirst(str_replace("_", " ", $str));
                    }, array_keys($header_data[0])));
            $this->objExcel->setActiveSheetIndex(0);
            $this->objExcel->getActiveSheet()->setTitle("EODB");
            $this->objExcel->getActiveSheet()->fromArray($header, null, 'A1');
            $this->objExcel->getActiveSheet()->fromArray($header_data, null, 'A2');
            $rowcount = count($header_data) + 1;
            $highestRowWithData = $this->objExcel->getActiveSheet()->getHighestColumn();
            $this->objExcel->getActiveSheet()->getStyle('A1:' . $highestRowWithData . $rowcount)->applyFromArray($this->styleArray);
            $this->downloadExcel($this->objExcel);
        }
    }

    public function getMISStatusExcel() {

        $year = $_POST['year'];
        $department = $_POST['department'];
        $Service_Used = $_POST['Service_Used'];
        $department_id = $_POST['department_id'];
        $survey_month = $_POST['survey_month'];
        $record_type = $_POST['record_type'];
        
        $where_1 = ''; $where_2 = ''; $where_3 = '';

        // sub group
        if(isset($_POST['m_group_id'])){
            $sql = "SELECT reform_number , services_name FROM  m_group  WHERE m_group_id = {$_POST['m_group_id']}";
            $rs = $this->db->getAll($sql);
            $where_3 = " AND fcd.Service_Used MEMBER OF('{$rs[0]['services_name']}') AND fcd.Reform_Number MEMBER OF('{$rs[0]['reform_number']}')  ";
        }

        if(!empty($department) && $department != -1){
            $where_1 .= " AND fcd.Department = '{$department}' ";
        }

        if(!empty($Service_Used) && $department != -1){
            $where_1 .= " AND fcd.department_id = '{$department_id}' AND fcd.Service_Used = '{$Service_Used}'";
        }

        if(!empty($survey_month) && $survey_month != -1){
            $where_1 .= " AND fcd.Month ='{$survey_month}'";
        }
 

        if($record_type == 'valid'){
            $where_2 =  " AND  errStatus = 'Valid'" ;
        } 
        else if($record_type == 'invalid'){
            $where_2 =  " AND  errStatus = 'Invalid'" ;
        }
        else if($record_type == 'completed'){
            $where_2 =  " AND  survey_comp_status = 'Completed' AND  errStatus = 'Valid'" ;
        }
        else if($record_type =='notinterest'){
            $where_2 =  " AND survey_comp_status = 'Not Interested' AND  errStatus = 'Valid'" ;
        } 
        else if($record_type == 'pending'){
            $where_2 =  " AND survey_comp_status NOT IN ('Completed','Not Interested') AND  errStatus = 'Valid'" ;
        }  


        $sql = "SELECT id,S_No, Reform_Number,Department, Service_Used, date_of_application, Date_of_Final_Approval, Name_of_Firm, 
        Contact_Address, firm_city, District, Pincode, Contact_Person, Designation, E_mail, Mobile_Numer, firm_additionalMobile, errStatus, dataError 
        FROM firm_company_details fcd WHERE fcd.deleted = 0 AND fcd.Survey_Year = '{$year}' {$where_1} {$where_2} {$where_3}";

        $header_data = $this->db->getAll($sql);
     
        if ($header_data) {
            $header = (array_map(function ($str) {
                        return ucfirst(str_replace("_", " ", $str));
                    }, array_keys($header_data[0])));
            $this->objExcel->setActiveSheetIndex(0);
            $this->objExcel->getActiveSheet()->setTitle("EODB");
            $this->objExcel->getActiveSheet()->fromArray($header, null, 'A1');
            $this->objExcel->getActiveSheet()->fromArray($header_data, null, 'A2');
            $rowcount = count($header_data) + 1;
            $highestRowWithData = $this->objExcel->getActiveSheet()->getHighestColumn();
            $this->objExcel->getActiveSheet()->getStyle('A1:' . $highestRowWithData . $rowcount)->applyFromArray($this->styleArray);
            $this->downloadExcel($this->objExcel);
        }
    }

    public function getReportExcel() {

        $header_data = $this->report->getReportList();

        if ($header_data) {
            $header = (array_map(function ($str) {
                return ucfirst(str_replace("_", " ", $str));
            }, array_keys($header_data[0])));
            $this->objExcel->setActiveSheetIndex(0);
            $this->objExcel->getActiveSheet()->setTitle("EODB");
            $this->objExcel->getActiveSheet()->fromArray($header, null, 'A1');
            $this->objExcel->getActiveSheet()->fromArray($header_data, null, 'A2');
            $rowcount = count($header_data) + 1;
            $highestRowWithData = $this->objExcel->getActiveSheet()->getHighestColumn();
            $this->objExcel->getActiveSheet()->getStyle('A1:' . $highestRowWithData . $rowcount)->applyFromArray($this->styleArray);
            $this->downloadExcel($this->objExcel);
        }
    }

    public function getAreaOfindingExcel() {

        $header_data = $this->area_of_findings->getAreaFindingList(true);

        if ($header_data) {
            $header = (array_map(function ($str) {
                return ucfirst(str_replace("_", " ", $str));
            }, array_keys($header_data[0])));
            $this->objExcel->setActiveSheetIndex(0);
            $this->objExcel->getActiveSheet()->setTitle("EODB");
            $this->objExcel->getActiveSheet()->fromArray($header, null, 'A1');
            $this->objExcel->getActiveSheet()->fromArray($header_data, null, 'A2');
            $rowcount = count($header_data) + 1;
            $highestRowWithData = $this->objExcel->getActiveSheet()->getHighestColumn();
            $this->objExcel->getActiveSheet()->getStyle('A1:' . $highestRowWithData . $rowcount)->applyFromArray($this->styleArray);
            $this->downloadExcel($this->objExcel);
        }
    }

    private function downloadExcel() {

        foreach ($this->objExcel->getWorksheetIterator() as $worksheet) {
            $this->objExcel->setActiveSheetIndex($this->objExcel->getIndex($worksheet));
            $sheet = $this->objExcel->getActiveSheet();
            $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            /** @var PHPExcel_Cell $cell */
            foreach ($cellIterator as $cell) {
                $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
                $this->objExcel->getActiveSheet()->getStyle($cell->getColumn() . '1')->getFill()->applyFromArray($this->headerstyle);
                $this->objExcel->getActiveSheet()->getStyle($cell->getColumn() . '1')->getFont()->getColor()->setRGB("FFFFFF");
            }
        }
        $this->objExcel->getProperties()
                ->setCreator('EODB')
                ->setLastModifiedBy('EODB')
                ->setTitle('EODB')
                ->setDescription('EODB');

        header('Content-Type: application/vnd.ms-excel');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($this->objExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }
}
