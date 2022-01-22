<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//https://toptierholidays.com/tour/the-statue-of-unity-tour-package/

class Management_model extends MY_Model {

    public function __construct(){
        parent::__construct();
        is_adminlogged_in();
    }

    public function getOrderReport($ssql, $offset = '', $limit = ''){
		
        $query = "SELECT a.*,b.name AS UserName,b.phone AS UserPhone,b.email AS UserEmail, c.order_message AS OrderStatus FROM `orders` a LEFT JOIN med_users b ON a.user_id = b.id LEFT JOIN order_status c ON a.order_status = c.id WHERE $ssql ORDER BY id DESC LIMIT " . $limit;
        $orderData = $this->db->query($query)->result_array();
		
		if( count($orderData) > 0){			
			foreach ($orderData as $key => $order) {
				$orderDetailQ = "SELECT a.*,b.category_name AS CategoryName 
								FROM `order_details` a
								LEFT JOIN categories b ON a.category_id = b.id 
								WHERE a.order_id = '". $order['order_id'] ."'";
				$orderDetail = $this->db->query($orderDetailQ)->result_array();
				if( count($orderDetail) > 0) {
					$orderData[$key]['details'] = $orderDetail;
				}
			}
		}
		
        return $orderData;
    }
}
?>
