<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends MY_Model {

    public function __construct(){
        parent::__construct();
    }

    public function isAdminValid($usename, $password) {
        $cond = [
			'isActive'=>'Yes',
			'userType' => 'Admin',
			'email' => $usename
		];
        $query = $this->db->select('*')
                        ->from('med_users')
                        ->where($cond)
                        ->get();
        $resultQ = $query->result_array();

		if(! empty($resultQ[0]['id']) && verifyPassword($password, $resultQ[0]['password'])) {
            $resultQ['Action'] = "TRUE";
            return $resultQ;
        }
    }
    
}
