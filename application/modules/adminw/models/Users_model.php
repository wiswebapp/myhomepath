<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends MY_Model {

    public function __construct(){
        parent::__construct();
        is_adminlogged_in();
    }

    public function getRegisterUsers($fields = '', $offset = '', $limit = '', $ssql = ''){

        $tablefields = empty($fields) ? '*' : $fields;
        if(empty($ssql)){
            $ssql = " 1=1 ";
        }
    	if(!empty($limit)){
            $offset = isset($offset) ? $offset : 0;
            $limitQ = " LIMIT $offset, $limit";
        }

    	$query ="SELECT ".$tablefields." FROM `med_users` WHERE deleted_at IS NULL AND $ssql ORDER BY `id` DESC ". $limitQ;
     	$op = $this->db->query($query);

        $result['count'] = $op->num_rows();
    	$result['data'] = $op->result_array();
        
    	return $result;
    }

    public function getAdminUsers($fields = '', $offset = '', $limit = '', $ssql = ''){

        if(!empty(trim($_GET['name']))){
            $ssql .= " AND (adm.vFirstName LIKE '%".trim($_GET['name'])."%' OR adm.vLastName LIKE '%".trim($_GET['name'])."%') ";
        }
        if(!empty(trim($_GET['email']))){
            $ssql .= " AND adm.vEmail LIKE '%".trim($_GET['email'])."%' ";
        }
        if(!empty(trim($_GET['role']))){
            $ssql .= " AND adm.iGroupId LIKE '%".trim($_GET['role'])."%' ";
        }
        if(!empty(trim($_GET['status']))){
            $ssql .= " AND adm.eStatus = '".trim($_GET['status'])."' ";
        }
        if(!empty($limit)){
            $offset = isset($offset) ? $offset : 0;
            $limitQ = " LIMIT $offset, $limit";
        }

        //$query ="SELECT ".$tablefields." FROM `administrators` WHERE eStatus != 'Deleted' $ssql ORDER BY `iAdminId` ASC ".$limitQ;
        $query = "SELECT adm.*, ag.vGroup FROM administrators adm INNER JOIN admin_groups ag ON adm.iGroupId = ag.iGroupId WHERE adm.eStatus != 'Deleted' $ssql $limitQ";
        $op = $this->db->query($query);

        $result['count'] = $op->num_rows();
        $result['data'] = $op->result_array();
        
        return $result;
    }

    public function getAgentUser($fields = '', $offset = '', $limit = '', $ssql = ''){

        $tablefields = empty($fields) ? '*' : $fields;
        if(empty($ssql)){
            $ssql = " 1=1 ";
        }
        if(!empty($limit)){
            $offset = isset($offset) ? $offset : 0;
            $limitQ = " LIMIT $offset, $limit";
        }

        $query ="SELECT ".$tablefields." FROM `register_agent` WHERE eStatus != 'Deleted' AND $ssql ORDER BY `iAgentId` DESC ".$limitQ;
        $op = $this->db->query($query);

        $result['data'] = $op->result_array();
        $result['count'] = $op->num_rows();
        
        return $result;
    }

    public function getOrganizations($fields = '', $offset = '', $limit = '', $ssql = ''){

        $tablefields = empty($fields) ? '*' : $fields;
        if(empty($ssql)){
            $ssql = " 1=1 ";
        }
        if(!empty($limit)){
            $offset = isset($offset) ? $offset : 0;
            $limitQ = " LIMIT $offset, $limit";
        }

        $query ="SELECT ".$tablefields." FROM `organization` WHERE eStatus != 'Deleted' AND $ssql ORDER BY `iOrgId` DESC ".$limitQ;
        $op = $this->db->query($query);

        $result['data'] = $op->result_array();
        $result['count'] = $op->num_rows();
        
        return $result;
    }
}
