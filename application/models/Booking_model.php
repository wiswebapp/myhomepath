<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends MY_Model {

    public function __construct(){
        parent::__construct();
    }

    public function getOrderFullDetail($orderId)
    {	
    	$ssql = "vOrderId = '$orderId'";
    	$data = $this->__getsingledata('orders','*',$ssql);
    	if(!empty($data[0])){

            $orderData = $data[0];
            $ssqlUser = 'iUserId = '.$orderData['iUserId'];
            $Userdata = $this->__getsingledata('register_user','*',$ssqlUser);
    		
            $returnArr['OrderData'] = $orderData;
            $returnArr['OrderUser'] = $Userdata[0];
            
            return $returnArr;
    	}
    	
    }
}

?>