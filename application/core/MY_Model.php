<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	/*============================For Single Website Purpose Only Start============================*/
	public function getProductData($ssql, $dataLimit ='',$offset =''){
        $data = array();
        if(! empty($ssql)){
            if(empty($dataLimit)){
                $dataLimit = NULL;
            }
            $data = $this->__getwheredata('products','*',$ssql,'id','DESC',$dataLimit,$offset);
			foreach($data as $key => $product){
				$ssql = 'id = ' . $product['category_id'];
				$catData = $this->getServiceCategory($ssql);
				$data[$key]['category_name'] = $catData[0]['category_name'];
			}
        }
        
        return $data;
    }

    public function getServiceCategory($ssql, $dataLimit ='',$offset =''){
        $data = array();
        if(!empty($ssql)){
            if(empty($dataLimit)){
                $dataLimit = NULL;
            }
            $data = $this->__getwheredata('categories','*',$ssql,'id','DESC',$dataLimit,$offset);
        }

        return $data;
    }
	
    public function getSubscriptionPlanMyModl($ssql, $dataLimit ='',$offset =''){
        $data = array();
        if(!empty($ssql)){
            if(empty($dataLimit)){
                $dataLimit = NULL;
            }
            //$data = $this->__getwheredata('subscription_plan','*',$ssql,'iPlanId','DESC',$dataLimit,$offset);
            $data = $this->db
					->select('sp.*,pro.vProductName AS ProductName')
					->from('subscription_plan sp')
					->where($ssql)
					->join('product pro', 'pro.iProductId = sp.iProductId')
					->limit($dataLimit,$offset)
					->get();
        }
        return $data->result_array();
    }

    public function getServiceType($ssql = '', $dataLimit ='',$offset =''){

        if(empty($ssql)){ $ssql = "eStatus = 'Active'"; }
        if(empty($dataLimit)){
            $dataLimit = NULL;
        }
        $data = $this->__getwheredata('service_type','*',$ssql,'iServiceTypeId','ASC',$dataLimit,$offset);
        return $data;
        
    }

	public function getLoginData($loginId, $userType){

		$loginId = decodeData($loginId);
		$tablename = "register_agent";
		$pid = "iAgentId";

		$userType = ucfirst(strtolower($userType));
		
		if($userType == "User"){
			$tablename = "register_user";
			$pid = "iUserId";
		}

		$data = $this->db
					->select('*')
					->from($tablename)
					->where(array( $pid=>$loginId,'eStatus'=>'Active'))
					->get();

		$result = $data->result();
		return $result[0];
	}

	public function getLanguageLabel($labelIdentifier = '', $labelIdentifierType = ''){
        if( !empty($labelIdentifier) && !empty($labelIdentifierType) ){
            $this->db->where($labelIdentifierType , $labelIdentifier);
        }

        $cond = array('eStatus'=>'Active');

        $query = $this->db->select('vLabel,vValue')
                        ->from('language_label')
                        ->where($cond)
                        ->get();

        if($query->num_rows() > 1 ){
            $labels = $query->result_array();    

            foreach ($labels as $value) {
                $op[$value['vLabel']] = $value['vValue'];
            }
        }else{
            $op = $query->result();
        }
        
        return $op;
    }

    /*============================For Single Website Purpose Only End============================*/

    public function __runCustomQuery($query){

    	$runQ = $this->db->query($query);
    	return $runQ->result_array();
    }

	public function getUpdateConditionFromArray($array){
		$ssql = "";
		$counter = 0;
		foreach($array as $key => $value){
			
			$ssql .= "`$key`  =  '$value'";
			if(count($array) > 0 && count($array)-1 != $counter){
				$ssql .= ", ";
			}
			$counter++;
		}
		return $ssql;
	}

	public function __getalldata($tablename,$tablefield=array(),$orderbyfield = 'id',$ascdesc = 'desc',$limit = 200)
	{
		if(is_array($tablefield)){$tablefield = implode(',',$tablefield);}
		$data = $this->db
					->select($tablefield)
					->from($tablename)
					->order_by($orderbyfield, $ascdesc)
					->limit($limit)
					->get();
		return $data->result_array();
	}

	public function __getwheredata($tablename,$tablefield='',$where=array(),$orderbyfield = 'id',$ascdesc = 'desc',$limit = 200,$offset='')
	{
		if(is_array($tablefield)){$tablefield = implode(',',$tablefield);}
		$data = $this->db
					->select($tablefield)
					->from($tablename)
					->where($where)
					->order_by($orderbyfield, $ascdesc)
					->limit($limit,$offset)
					->get();
		return $data->result_array();
	}

	public function __getsingledata($tablename,$tablefield=array(),$where=array())
	{
		if(is_array($tablefield)){$tablefield = implode(',',$tablefield);}
		$data = $this->db
					->select($tablefield)
					->from($tablename)
					->where($where)
					->get();
		return $data->result_array();
	}
	public function __getsearchdata($tablename,$tablefield=array(),$like=array(),$orderbyfield = 'id',$ascdesc = 'desc',$limit = 200)
	{
		if(is_array($tablefield)){$tablefield = implode(',',$tablefield);}
		$data = $this->db
					->select($tablefield)
					->from($tablename)
					->like($like)
					->order_by($orderbyfield, $ascdesc)
					->limit($limit)
					->get();
		return $data->result();
	}

	public function __getdatacount($tablename, $where = '') {
		if(empty($where)) { $where = '1 = 1'; }
		$data = $this->db
					->select('*')
					->from($tablename)
					->where($where)
					->get();
		/*echo $this->db->last_query();exit;*/
		return $data->num_rows();
	}

	public function __add_single_data($tablename,$data=array())
	{
		$add = $this->db->insert($tablename, $data);
		if($add){ 
			return $this->db->insert_id(); 
		}
	}
	public function __update_single_data($tablename,$data=array(),$condition=array())
	{
		$this->db->where($condition);
		$update_status = $this->db->update($tablename, $data);
		//return $update_status;
		return $this->db->affected_rows();
	}
	public function __delete_single_data($tablename,$condition=array())
	{
		$this->db->where($condition);
		$delete_status = $this->db->delete($tablename);
		//return $update_status;
		return $this->db->affected_rows();
	}
	public function __update_multiple_data($tablename,$data=array(),$field,$dataIdArray)
	{
		//$dataIdArray = 1,2,3,4,5
		$this->db->where_in($field, $dataIdArray);
		$update_status = $this->db->update($tablename, $data);
		//return $update_status;
		return $this->db->affected_rows();

	}
	public function getCountryList($where = array(), $field = '*'){
		if(empty($where)){
			$where = array('1'=>'1');
		}
		$this->db->where('eStatus','Active');
		$data = $this->db->select($field)->from('country')->where($where)->order_by('vCountry')->get();
		return $data->result_array();
	}
	public function getStateList($where = array()){
		if(empty($where)){
			$where = array('1'=>'1');
		}
		$this->db->where('eStatus','Active');
		$data = $this->db->select('*')->from('state')->where($where)->order_by('vState')->get();
		return $data->result_array();
	}
	public function getCityList($where = array('iCountryId'=>99)){
		if(empty($where)){
			$where = array('1'=>'1');
		}
		$this->db->where('eStatus','Active');
		$data = $this->db->select('*')->from('city')->where($where)->order_by('vCity')->get();
		return $data->result_array();
	}

	public function checkDuplicate($tablename,$fieldname,$fieldvalue,$verificationID = ''){
		if(!empty($verificationID)){
			/* 	For Example :: 
				$verificationID = "tablefield != 1"
			*/
			$this->db->where($verificationID);
		}
		$data = $this->db
					->select('*')
					->from($tablename)
					->where($fieldname,$fieldvalue)
					->get();
		return $data->num_rows();
	}

	public function checkifDateisBetween($startDate,$endDate,$actualDate)
	{
		$actualDate = date('Y-m-d', strtotime($actualDate));
		
		if (($actualDate >= $startDate) && ($actualDate <= $endDate)){
            $msg = 1;
        }else{
            $msg = 0;
        }
        return $msg;
	}

}
