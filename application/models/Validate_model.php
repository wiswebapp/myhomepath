<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validate_model extends MY_Model {

    public function __construct(){
        parent::__construct();
    }

    public function isEmailExist( $tableName, $fieldName, $emailId, $onlyActive = FALSE ){

        if($onlyActive == TRUE){
            $this->db->where('eStatus', 'Active');
        }
        
    	$query = $this->db->select('*')
                        ->from($tableName)
                        ->where($fieldName,$emailId)
                        ->get();
        $result = $query->num_rows();

        
        if( $result > 0 ){
        	$op = $query->result_array();
        }else{
            $op = $query->num_rows();
        }
        return $op;

    }

    public function isUserValid($usename,$password)
    {
        $resultQ = $this->isEmailExist('register_user','vEmail',$usename,TRUE);

        if( !empty($resultQ) ){
            
            if( verifyPassword($password,$resultQ[0]['vPassword']) ){
                $resultQ['Action'] = TRUE;
                $resultQ['Msg'] = "LOGIN_SUCCS";
                //if user is blocked
                if( $resultQ[0]['eStatus'] == "InActive" ){
                	$resultQ['Msg'] = "LOGIN_USER_INACTIVE";
                }
                if( $resultQ[0]['eStatus'] == "Blocked" ){
                	$resultQ['Msg'] = "LOGIN_USER_BLOCKED";
                }
            }
        }else{
        	$resultQ['Action'] = FALSE;
        }
        return $resultQ;
    }

    public function isAgentValid($usename,$password)
    {   
        $resultQ = array();
        $resultQ['Action'] = FALSE;
        $resultQ = $this->isEmailExist('register_agent',$usename,TRUE);
        $isFeepaid = $resultQ[0]['eIsFeePaid'];

        if( !empty($resultQ) && $isFeepaid == "Yes"){
            
            if( verifyPassword($password,$resultQ[0]['vPassword']) ){

                $resultQ['Action'] = TRUE;
                $resultQ['Msg'] = "LOGIN_SUCCS";
                //if user is blocked
                if( $resultQ[0]['eStatus'] == "InActive" ){
                    $resultQ['Msg'] = "LOGIN_USER_INACTIVE";
                }
                if( $resultQ[0]['eStatus'] == "Blocked" ){
                    $resultQ['Msg'] = "LOGIN_USER_BLOCKED";
                }
            }
        }
        return $resultQ;
    }

    public function isOrganizationValid($vUserEmail,$password)
    {
        $resultQ = $this->isEmailExist('organization','vUserEmail',$vUserEmail);
        //printr($this->db->last_query());
        if( !empty($resultQ) ){
            if( verifyPassword($password,$resultQ[0]['vPassword']) ){
                $resultQ['Action'] = TRUE;
                $resultQ['Msg'] = "LOGIN_SUCCS";
                //if user is blocked
                if( $resultQ[0]['eStatus'] == "InActive" ){
                    //$resultQ['Msg'] = "LOGIN_USER_INACTIVE";
                    /* Updating Status */
                    $data['eStatus'] = "Active";
                    $condition['vUserEmail'] = $vUserEmail;
                    $this->__update_single_data('organization',$data,$condition);
                }
                if( $resultQ[0]['eStatus'] == "Blocked" ){
                    $resultQ['Msg'] = "LOGIN_USER_BLOCKED";
                }
            }
        }else{
            $resultQ['Action'] = FALSE;
        }
        return $resultQ;
    }
}