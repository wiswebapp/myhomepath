<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_Model extends MY_Model {

    public function __construct(){
        parent::__construct();
        is_adminlogged_in();
    }

    /*====================================DASHBOARD FUNCTION====================================*/
    public function getWalletBalance( $userId, $userType )
    {
        $balance = 0;

        $tbl = "register_user";
        $primaryId = "iUserId";
        if($userType == "Agent"){
            $tbl = "register_agent";
            $primaryId = "iAgentId";
        }
        $where = array($primaryId => $userId);
        $checkUser = $this->__getsingledata($tbl,$primaryId,$where);//reffers to current model
        
        if($checkUser[0][$primaryId] > 0){

            $sql = "SELECT SUM(iBalance) as totcredit FROM user_wallet WHERE iUserId = '" . $userId . "' AND eUserType = '" . $userType . "' AND eType = 'Credit'";
            $db_credit_balance = $this->db->query($sql)->result_array();

            $sql = "SELECT SUM(iBalance) as totdebit FROM user_wallet WHERE iUserId = '" . $userId . "' AND eUserType = '" . $userType . "' AND eType = 'Debit'";
            $db_debit_balance = $this->db->query($sql)->result_array();

            $balance = $db_credit_balance[0]['totcredit'] - $db_debit_balance[0]['totdebit'];
            $balance = ($balance > 0) ? $balance : '0.00';
            return $balance;
        }
    }
    
    
}
