<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_front_model extends MY_Model {

	public function __construct(){
        parent::__construct();
    }

    public function resetPasswordModel($email,$userType){
            
        if(!empty($userType) && !empty($email)){

            if($userType == "User"){

                $where = array('eStatus'=>'Active','vEmail'=>$email);
                $getEmail = $this->__getsingledata('register_user','vEmail',$where);
                if(!empty($getEmail)){
                    $returnData = TRUE;
                }else{
                    $returnData = FALSE;
                }
            }
        }
        return $returnData;
    }

    public function getCMSPagesData($pageId = '',$forAPP = 'No'){

        $field = "*";
        if($forAPP != "No"){
            $field = "vPageName,tPageDesc";
        }
    	if(!empty($pageId)){
    		$this->db->where('iPageId', $pageId);
    	}
    	$data = $this->db
					->select($field)
					->from('pages')
					->get();

		$result = $data->result_array();
		return $result;
    }
    public function getProductModel($categoryId){

        $data = $this->db
                    ->select('*')
                    ->from('product')
                    ->where(array('eStatus'=>'Active','iCategoryId'=>$categoryId))
                    ->get();

        $resultData = $data->result_array();
        return $resultData;
    }
    public function getGeneralData(){
        
        $returnArr = $this->db->select('vName,vValue')
                                ->from('configurations')
                                ->where('eStatus','Active')
                                ->get();
        return $returnArr->result_array();
    }
    public function getOrderReport($userId,$userType,$paymentType = ''){
        
        if(!empty($paymentType)){
            $this->db->where('paymentType',$paymentType);
            $this->db->where(array('eStatus!='=>'Pending'));
        }
        
        $returnArr = $this->db->select('*')
                                ->from('orders')
                                ->where(array('iUserId'=>$userId))
                                ->get();
        return $returnArr->result_array();
    }
    public function getSingleOrderDetail($orderId){
        
        $query = "SELECT ord.*,pro.vProductName AS ProductName,sc.vCategory AS CategoryName,org.vUserName AS ProviderName,ru.vName AS UserName
        FROM `orders` ord 
        LEFT JOIN `register_user` ru ON ord.iUserId = ru.iUserId
        LEFT JOIN `organization` org ON ord.iProviderId = org.iOrgId
        LEFT JOIN `service_category` sc ON ord.iProductCatId = sc.iCategoryId
        LEFT JOIN `product` pro ON ord.iProductId = pro.iProductId
        WHERE ord.vOrderId = '$orderId'";
        $returnArr = $this->__runCustomQuery($query);
        
        $returnArr[0]['orderStatus'] = "Not Assigned";
        if(!empty($returnArr[0]['ProviderName'])){
            $returnArr[0]['orderStatus'] = "Assigned";
        }
        //printr($returnArr);
        return $returnArr;
    }

    public function getLogReport($userId,$startDate='',$endDate=''){
        
        if(!empty($startDate)){
            $startDate = date('Y-m-d',strtotime($startDate));
        }
        if(!empty($endDate)){
            $endDate = date('Y-m-d',strtotime($endDate));
        }
        $whr = "iProviderId = $userId";
        if(!empty($startDate) && !empty($endDate)){
            $whr .= " AND dDate BETWEEN '$startDate' AND '$endDate'";
        }

        $returnArr = $this->db->select('*')
                                ->from('provider_logs')
                                ->where($whr)
                                ->order_by('dDate','desc')
                                ->get();
        return $returnArr->result_array();
    }

    public function letLastLogReport($userId){
        
        $q1 = "SELECT * FROM `provider_logs` WHERE iProviderId = 1 AND eLogType = 'Started' ORDER BY iLogId DESC LIMIT 1";
        $q2 = "SELECT * FROM `provider_logs` WHERE iProviderId = 1 AND eLogType = 'Ended' ORDER BY iLogId DESC LIMIT 1";
        

        $returnArr1 = $this->__runCustomQuery($q1);
        $returnArr2 = $this->__runCustomQuery($q2);
        $arr['lastStart'] = toDate($returnArr1[0]['dDateTime'],'d-M-Y (h:i:s A)');
        $arr['lastEnd'] = toDate($returnArr2[0]['dDateTime'],'d-M-Y (h:i:s A)');

        return $arr;
    }

    public function getPendingOrders($userLat,$userLong){

        $data = array();

        if(!empty($userLat) && !empty($userLong)){

            $sql = "SELECT *, 
                               (6371 * acos( cos( radians('$userLat') ) * cos( radians(vAddressLat) ) * 
                                cos( radians(vAddressLong) - radians('$userLong')) + sin(radians('$userLat')) *
                                sin(radians(vAddressLat)) )) as distance 
                        FROM orders
                        WHERE eStatus = 'Placed' AND iProviderId <= 0
                        HAVING distance < 10";
            
            $data = $this->__runCustomQuery($sql);
            //printr($data);
        }

        return $data;
        
    }

}