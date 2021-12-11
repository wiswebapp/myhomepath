<?php
namespace modal;

use modal\Database;

class ApiFunctions
{
    public $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function createAccessToken($userId) {
        $accessToken = MD5("LOGINTOKEN_". rand(1111111111,9999999999)) . date('Ymdhis');
        $updateToken = $this->updateDataToDb('med_users', ['accessToken' => $accessToken], ['id' => $userId]);

        return $accessToken;
    }

    public function getDataFromDb($fields = '*', $table, $where = [], $orderBy = 'id DESC', $limit = NULL) {
        
        $i = 0;
        $result = [];

        $query = "SELECT $fields FROM `". $table ."`";
        if(! empty($where)) {
            $query .= " WHERE ";
            foreach ($where as $key => $value) {
                $keyArr = explode(' ', $key);                
                if(count($keyArr) > 1) {
                    if( is_null($value)) {
                        $query .= "$keyArr[0] $keyArr[1] null AND ";
                    }else {
                        $query .= "$keyArr[0] $keyArr[1] '$value' AND ";
                    }
                } else {
                    $query .= "$key = '$value' AND ";
                }
            }
        }

        $query = substr($query, 0, strlen($query) - 4) . " ORDER BY " .$orderBy;

        if($limit != NULL) {
            $query .= " LIMIT ". $limit;
        }
        
        $response = $this->conn->query($query);
    
        if($response->num_rows > 0) {
            while ($data = mysqli_fetch_assoc($response)) {
                $result[$i++] = $data;
            }
        }

        return $result;
    }

    public function addDataToDb($table, $data = []) {
        if(empty($data) || empty($table)) {
            return false;
        }
        
        $query = "INSERT INTO `" . $table . "` SET";
        foreach ($data as $key => $value) {
            $query .= " $key = '$value',";
        }
        $query = rtrim($query, ',');
        $result = $this->conn->query($query);

        return $this->conn->insert_id;
    }

    public function updateDataToDb($table, $data = [], $where = []) {
        if(empty($data) || empty($where) || empty($table)) {
            return false;
        }
        
        $query = "UPDATE `" . $table . "` SET";
        foreach ($data as $key => $value) {
            $query .= " `$key` = '". $value ."',";
        }
        $query = rtrim($query, ',');

        if(! empty($where)) {
            $query .= " WHERE ";
            foreach ($where as $key => $value) {
                $keyArr = explode(' ', $key);
                if (count($keyArr) > 1) {
                    $query .= "$keyArr[0] $keyArr[1] '$value' AND ";
                } else {
                    $query .= "`$key` = '". $value ."' AND ";
                }
            }
        }

        $query = substr($query, 0, strlen($query) - 4);
        $result = $this->conn->query($query);

        return $result;
    }

    public function removeDataToDb($table, $where = []) {
        if (empty($where) || empty($table)) {
            return false;
        }

        $query = "DELETE FROM `" . $table . "` ";
        if (!empty($where)) {
            $query .= " WHERE ";
            foreach ($where as $key => $value) {
                $keyArr = explode(' ', $key);
                if (count($keyArr) > 1) {
                    $query .= "$keyArr[0] $keyArr[1] '$value' AND ";
                } else {
                    $query .= "$key = '$value' AND ";
                }
            }
        }
        $query = substr($query, 0, strlen($query) - 4);
        
        $result = $this->conn->query($query);

        return $result;
    }

    public function checkUserExist($email = "", $phone = "") {
        $query = "SELECT * FROM `med_users` WHERE isActive = 'Yes' AND (email = '".$email."' || phone = '".$phone."')";
        $runQ = $this->conn->query($query);
        $reponse = $runQ->num_rows;
        if($reponse > 0) {
            return true;
        }

        return false;
    }

    public function verifyUser($email = "", $phone = "", $password) {
        $query = "SELECT * FROM `med_users` WHERE (email = '".$email."' || phone = '".$phone."') AND password = '".MD5($password)."' ";
        $runQ = $this->conn->query($query);
        $reponse = $runQ->num_rows;
        if($reponse > 0) {
            return (array)$runQ->fetch_object();
        }

        return false;
    }

    public function checkUserAuthenticationWithToken($user, $token){
       $query = "SELECT * FROM `med_users` WHERE id = '".$user."' AND accessToken = '".$token."' AND isActive = 'Yes' AND isLogin = 'Yes' ";
        $runQ = $this->conn->query($query);
        $reponse = $runQ->num_rows;
        if($reponse > 0) {
            return true;
        }

        return false;
    }
}

$ApiFunctions = new ApiFunctions();
