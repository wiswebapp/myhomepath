<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exportdata extends MY_Controller {

	public function __construct(){
		parent::__construct();
		is_adminlogged_in();
		$this->load->model('Users_model');
		$this->load->model('Management_model');
		$this->load->model('Common_model');
		$this->load->model('Booking_model');
	}
	public function register_users()
	{
		$exportType = isset($_GET['exportType']) ? trim($_GET['exportType']) : 'xls';

		$ssql = " 1 = 1";
		if(!empty(trim($_GET['name']))){
    		$ssql .= " AND vName LIKE '%".trim($_GET['name'])."%'";
    	}
    	if(!empty(trim($_GET['mobile']))){
    		$ssql .= " AND vPhone LIKE '%".trim($_GET['mobile'])."%' ";
    	}
    	if(!empty(trim($_GET['email']))){
    		$ssql .= " AND vEmail LIKE '%".trim($_GET['email'])."%' ";
    	}
        if(!empty(trim($_GET['status']))){
            $ssql .= " AND eStatus = '".trim($_GET['status'])."' ";
        }
		$fields = "vName AS Name,vPhone,vEmail,eStatus";
		$data = $this->Users_model->getRegisterUsers( $fields, '', '', $ssql );
		if($exportType == "pdf"){
			printr($data[]);
		}else{
		    $result = array();
		    foreach ($data['data'] as $key => $row) {
		        $exportdata = array();
		        $counter = $key + 1;

		        $exportdata['Sr. No.'] = $counter;
		        $exportdata['Name'] = $row['Name'];
		        $exportdata['Mobile'] = $row['vPhone'];
		        $exportdata['Email'] = $row['vEmail'];
		        $exportdata['Status'] = $row['eStatus'];
		        $result[] = $exportdata;
		    }
		    
		    $filename = 'register_user_' . time() . ".xls";
			
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
		   
		    echo implode("\t", array_keys($result[0])) . "\r\n";
		    foreach ($result as $value) {
		    	foreach ($value as $key => $val) {
		        	if ($key == 'Name') { $val = trim($val); }
		        	if ($key == 'Email') { $val = trim($val); }
		            echo $val . "\t";
		        }
		            echo "\r\n";
		    }
		}
	}

}