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

    public function getDataFromDb($fields = '*', $table, $where = [], $orderBy = 'id DESC', $limit = NULL) {
        
        $i = 0;
        $result = [];

        $query = "SELECT $fields FROM `". $table ."`";
        if(! empty($where)) {
            $query .= " WHERE ";
            foreach ($where as $key => $value) {
                $keyArr = explode(' ', $key);                
                if(count($keyArr) > 1) {
                    $query .= "$keyArr[0] $keyArr[1] '$value' AND ";
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

    public function createAccessToken($userId) {
        $accessToken = MD5("LOGINTOKEN_". rand(1111111111,9999999999)) . date('Ymdhis');
        $this->updateDataToDb('med_users', ['accessToken' => $accessToken,''], ['id' => $userId]);
        return $accessToken;
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
            $query .= " $key = '$value',";
        }
        $query = rtrim($query, ',');

        if(! empty($where)) {
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

    public function createSP($data) {
        $data['password'] = MD5($data['password']);

        if( $this->checkSpEmailExist($data['email']) ) {
            $query = "INSERT INTO `service_provider`(`name`, `email`, `password`) VALUES ('".$data['name']."', '".$data['email']."', '".$data['password']."')";
            $runq = $this->conn->query($query);
            if($runq) {
                $lastInsertId = $this->conn->insert_id;
                $response['success'] = true;
                $response['message'] = $data;
                $response['message']['id'] = $lastInsertId;
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Email Already exist";
        }

        return $response;
    }

    public function updateSP($data) {
        
        if(! empty($data['password'])) {
            $data['password'] = MD5($data['password']);
        }

        if ($this->checkSpEmailExist($data['email'], $data['id'])) {
            $query = "UPDATE `service_provider` SET ";
            foreach ($data as $key => $value) {
                if($key == 'id') { continue; }

                if(! empty($value)) {
                     $query .= $key . " = '" . $value . "', ";
                }
            }
            $query .= "updated_at = '".date('Y-m-d h:i:s')."' WHERE id = ". $data['id'];
            
            $runq = $this->conn->query($query);
            if ($runq) {
                $lastInsertId = $this->conn->insert_id;
                $response['success'] = true;
                $response['message'] = $this->getDataFromDb('*', 'service_provider', ['id =' => $data['id']]);
            }
        } else {
            $response['success'] = false;
            $response['message'] = "Email Already exist";
        }

        return $response;
    }

    public function verifySP($data) {
        $dataId = $data['id'];
        unset($data['id']);
        $result = $this->updateDataToDb('service_provider', $data, ['id' => $dataId]);
        if($result) {
            $response['success'] = true;
            $response['message'] = $this->getDataFromDb('*', 'service_provider', ['id =' => $dataId]);
        } else {
            $response['success'] = false;
            $response['message'] = 'Error while updating data';
        }

        return $response;
    }

    public function getServiceeProvider($serviceId, $subServiceId) {
        $i = 0;
        $result = [];

        $query = "SELECT sp.* FROM `service_provider` sp LEFT JOIN service_provider_services spl ON spl.serviceProviderId = sp.id WHERE sp.isOnline = 1 AND sp.status = 1 AND sp.isVerified = 1 AND spl.serviceId = $serviceId AND spl.subServiceId = $subServiceId";
        
        $response = $this->conn->query($query);

        if ($response->num_rows > 0) {
            while ($data = mysqli_fetch_assoc($response)) {
                $result[$i++] = $data;
            }
        }

        return $result;
    }

    public function generateBookingId() {
        $prefix = "AIO" . date('Ymd');
        $random = time() . rand(0,100);
        $last_number = str_replace($prefix, "", $random);
        $counter = intval(ltrim($last_number, "0")) + 1;
        $booking_number = $prefix . str_pad($counter, 3, 0, STR_PAD_LEFT);

        return $booking_number;
    }

    public function getListOfBookedService($serviceProviderId) {
        $i = 0;
        $result = [];

        $query = "SELECT * FROM `service_booking` sb LEFT JOIN `service_list` sl ON sb.subServiceId = sl.id WHERE sb.serviceProviderId = '".$serviceProviderId."' ORDER BY sb.id DESC";

        $response = $this->conn->query($query);

        if ($response->num_rows > 0) {
            while ($data = mysqli_fetch_assoc($response)) {
                $result[$i++] = $data;
            }
        }

        return $result;
    }
}

$ApiFunctions = new ApiFunctions();
