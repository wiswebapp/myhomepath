<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_model extends MY_Model {

	public function __construct(){
        parent::__construct();
        is_adminlogged_in();
    }

    public function getAllCMSPages($pageId = ''){

    	if(!empty($pageId)){
    		$this->db->where('iPageId', $pageId);
    	}
    	$data = $this->db
					->select('*')
					->from('pages')
					->get();

		$result = $data->result_array();
		return $result;
    }

    public function getSubscriptionPlan($iCategoryId = ''){

        if(!empty($iCategoryId)){
            $this->db->where('iCategoryId', $iCategoryId);
        }
        $data = $this->db
                    ->select('*')
                    ->from('subscription_plan')
                    ->where('eStatus','Active')
                    ->get();

        $result = $data->result_array();
        return $result;
    }
    public function getSingleSubscriptionPlan($subscriptionPlanId){

        
        $data = $this->db
                    ->select('*')
                    ->from('subscription_plan')
                    ->where('iPlanId',$subscriptionPlanId)
                    ->get();

        $result = $data->result_array();
        return $result;
    }
}
