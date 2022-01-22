<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//https://toptierholidays.com/tour/the-statue-of-unity-tour-package/

class Management_model extends MY_Model {

    public function __construct(){
        parent::__construct();
        is_adminlogged_in();
    }

    public function getOrderReport( $ssql, $offset = '', $limit = ''){

        $query = "SELECT ord.*,cat.vCategory AS CategoryName,prod.vProductName AS ProductName,ru.vName AS CustName,org.vUserName AS ProviderNm
        	FROM `orders` ord LEFT JOIN `service_category` cat ON ord.iProductCatId = cat.iCategoryId LEFT JOIN `product` prod ON ord.iProductId = prod.iProductId
        	LEFT JOIN `register_user` ru ON ord.iUserId = ru.iUserId
        	LEFT JOIN `organization` org ON ord.iProviderId = org.iOrgId
        	WHERE $ssql ORDER BY ord.iOrderId DESC $limitQ";
        
        $dataR = $this->db->query($query)->result_array();
        return $dataR;
    }
}

?>