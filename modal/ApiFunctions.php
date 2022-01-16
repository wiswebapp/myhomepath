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

    public function getProductList($productId = ''){
        $result = [];
        $where = "";

        if(! empty($productId)) {
            $where = "AND pro.id = ". $productId;
        }

        $query = "SELECT pro.*,cat.category_name FROM `products` pro 
        LEFT JOIN categories cat ON pro.category_id = cat.id
        WHERE pro.status = 'Active' AND pro.availblity = 'Yes' ".$where." ORDER BY pro.updated_at DESC";

        $response = $this->conn->query($query);
        $count = $response->num_rows;
        if($count > 0) {
            while($row = $response->fetch_assoc()) {
                if(! empty($row['product_image'])) {
                    $productImage = BASE_URL . "/webimages/productImage/" . $row['product_image'];
                    $row['product_image'] = $productImage;
                }
                $result[] = $row;        
            }
        }

        return $result;
    }

    /**
     * int @userId User Id
     * int @flag 0 => All Status
    */
    public function getListOfOrder($userId = 0, $flag = 0) {
        $result = [];
        
        $orderStatusQ = ($flag != 0) ? " AND order_status = '". $flag. "' " : "";
        $userQ = ($userId != 0) ? " AND userId = '". $userId. "' " : "";

        $query = "SELECT * FROM orders WHERE 1 = 1 $userQ $orderStatusQ ";
        $response = $this->conn->query($query);
        $count = $response->num_rows;
        if($count > 0) {
            while($row = $response->fetch_assoc()) {
                $row['productList'] = $this->getDataFromDb('*', 'order_details', ['order_id' => $row['order_id']]);
                $address = json_decode($row['productList'][0]['order_address'], TRUE);
                $row['address'] = $address['address']. "," .$address['locality']. "," .$address['landmark']. "," .$address['city']. "," .$address['pincode'];
                $result[] = $row;
            }
        }

        return $result;   
    }

    public function getListOfOrderForAdmin() {
        $result = [];
        $overAllCount = 0;

        $placedOrderCount = count($this->getListOfOrder(0, 1));
        $result['placedOrder'] = $this->getListOfOrder(0, 1);

        $acceptedOrderCount = count($this->getListOfOrder(0, 2));
        $result['acceptedOrder'] = $this->getListOfOrder(0, 2);

        $canceledOrderCount = count($this->getListOfOrder(0, 3));
        $result['canceledOrder'] = $this->getListOfOrder(0, 3);

        $deliveredOrderCount = count($this->getListOfOrder(0, 5));
        $result['deliveredOrder'] = $this->getListOfOrder(0, 5);

        $declinedOrderCount = count($this->getListOfOrder(0, 6));
        $result['declinedOrder'] = $this->getListOfOrder(0, 6);

        $overAllCount = ($placedOrderCount + $acceptedOrderCount + $canceledOrderCount + $deliveredOrderCount + $declinedOrderCount);
        
        return [
            'orderData' => $result,
            'count' => $overAllCount,
        ];   
    }

    public function checkUserExist($email = "", $phone = "", $userType = 'User') {

        $query = "SELECT * FROM `med_users` WHERE userType = '".$userType."' AND isActive = 'Yes' AND (email = '".$email."' || phone = '".$phone."')";
        $runQ = $this->conn->query($query);
        $response = $runQ->num_rows;
        if($response > 0) {
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

    public function isAdmin($userId) {
        $query = "SELECT userType FROM `med_users` WHERE id = '".$userId."' ";
        $runQ = $this->conn->query($query);
        $reponse = $runQ->num_rows;
        if($reponse > 0 && $data = $runQ->fetch_array()) {
            if($data['userType'] == "User") {
                return false;
            }

            return true;
        }

        return false;
    }
}

$ApiFunctions = new ApiFunctions();
