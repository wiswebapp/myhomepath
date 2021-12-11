<?php
namespace controller;

use modal\ApiFunctions;

class ApiController {

    public $ApiFunctions;

	public function __construct(){
        $this->apiFunctions = new ApiFunctions();
    }

    public function isAuthorized($request) {
        $unAuthoriedCalls = ['login', 'register'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if($requestMethod != "POST") {
            $this->sessionOut('Invalid Method Use');
        }

        if(empty($request) || ! in_array($request, $unAuthoriedCalls)) {
            $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : "";
            $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
            $checkUserLogin = $this->apiFunctions->checkUserAuthenticationWithToken($user, $token);
            if(! $checkUserLogin ) {
                $this->sessionOut('Required parameter missing');
            }
        }
    }

    public function register() {
        $data['name'] = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : "";
        $data['email'] = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "";
        $data['phone'] = isset($_REQUEST['number']) ? trim($_REQUEST['number']) : "";
        $data['password'] = isset($_REQUEST['password']) ? MD5(trim($_REQUEST['password'])) : "";

        if(empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['password'])) {
            $response['success'] = false;
            $response['message'] = "Required parameter missing";
            $this->setResponse($response);
        }

        if($this->apiFunctions->checkUserExist($data['email'] , $data['phone'])) {
            $response['success'] = false;
            $response['message'] = "User Already Exist";
            $this->setResponse($response);   
        }

        $registerUser = $this->apiFunctions->addDataToDb('med_users', $data);
        if($registerUser) {
            $userData = $this->apiFunctions->getDataFromDb('*', 'med_users', ['id' => $registerUser]);
            $response['success'] = true;
            $response['message'] = "User Registerd successfully";    
            $response['data'] = $userData[0];
        } else {
            $response['success'] = false;
            $response['message'] = "Some Error Occured";    
        }
        
        $this->setResponse($response);
    }

    public function login() {
        $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "";
        $phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : "";
        $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : "";
        $isSuccess = false;
        $responseMsg = "You are not registerd";

        if($this->apiFunctions->checkUserExist($email, $phone)) {
            $verifyUser = $this->apiFunctions->verifyUser($email, $phone, $password);
            $isSuccess = false;
            $message = "Invalid Username / Password";
            
            if($verifyUser) {
                $where = ['id' => $verifyUser['id']];
                $this->apiFunctions->updateDataToDb('med_users', ['isLogin' => 'Yes'], $where);
                $this->apiFunctions->createAccessToken($verifyUser['id']);
                $data = $this->apiFunctions->getDataFromDb('*', 'med_users', $where);
                $isSuccess = true;
                $responseMsg = "Login Success";
                $response['data'] = $data[0];
            }
        }

        $response['success'] = $isSuccess;
        $response['message'] = $responseMsg;
        $this->setResponse($response);
    }

    public function setResponse($response) {
        $response['API_VERSION'] = "1.0";
        $response['API_HASH'] = MD5("asdjh37rfy93yfh9wy9f3f3uyrh73r");
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit;
    }

    public function sessionOut($message = ''){
        $message = !empty($message) ? $message : 'SESSION_OUT';
        if ($message == 'MISSING_PARAM'){
            $message = "Something Went Wrong in Parameters";
        }
        $returnArr['success'] = false;
        $returnArr['message'] = $message;
        
        $this->setResponse($returnArr);
    }

    public function getProductList() {
        $where = [
            'status' => 'Active',
            'deleted_at IS' => null,
            'availblity' => 'Yes',
        ];
        $dataList = $this->apiFunctions->getDataFromDb('*', 'products', $where);
        if (! empty($dataList)){
            $result['success'] = true;
            $result['data'] = $dataList;
            $result['message'] = count($dataList) . " product Found";
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry No product found.';
        }
        $this->setResponse($result);
    }

    public function getCategoryList() {
        $where = [
            'parent_id' => 0,
            'status' => 'Active',
            'deleted_at IS' => null
        ];
    	$catList = $this->apiFunctions->getDataFromDb('*', 'categories', $where);
        if (! empty($catList)){
            $result['success'] = true;
            $result['data'] = $catList;
            $result['message'] = count($catList) . " Category Found";
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry No Category found.';
        }
        $this->setResponse($result);
    }

    public function addCategoryList(){
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";
        
        if(empty($name)) {
            $result['success'] = false;
            $result['message'] = 'Please add Category name';
            $this->setResponse($result);
        }

        $data = ['category_name' => $name,'deleted_at IS' => null];
        $checkData = $this->apiFunctions->getDataFromDb('*', 'categories', $data,'id DESC',1);
        if(count($checkData) > 0) {
            $result['success'] = false;
            $result['message'] = 'Category name '. $name .' already exist';
            $this->setResponse($result);
        }

        $data = ['category_name' => $name];
        $addToDB = $this->apiFunctions->addDataToDb('categories', $data);
        if (! empty($addToDB)){
            $result['success'] = true;
            $result['data'] = $catList = $this->apiFunctions->getDataFromDb('*', 'categories', ['id' => $addToDB]);
            $result['message'] = "Category Added successfully !";
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry No Category found.';
        }
        $this->setResponse($result);
    }

    public function editCategoryList(){
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
        
        if(empty($name) || empty($id)) {
            $result['success'] = false;
            $result['message'] = 'Please add Category name';
            $this->setResponse($result);
        }

        $data = ['id' => $id];
        $checkData = $this->apiFunctions->getDataFromDb('*', 'categories', $data,'id DESC',1);
        if( empty($checkData)) {
            $result['success'] = false;
            $result['message'] = 'No categorie found';
            $this->setResponse($result);
        }
        
        if($checkData[0]['category_name'] == $name) {
            $result['success'] = true;
            $result['message'] = 'No Changes made';
            $this->setResponse($result);
        }

        $data = ['category_name' => $name];
        $addToDB = $this->apiFunctions->updateDataToDb('categories', $data, ['id' => $id]);
        if (! empty($addToDB)){
            $result['success'] = true;
            $result['data'] = $catList = $this->apiFunctions->getDataFromDb('*', 'categories', ['id' => $id]);
            $result['message'] = "Category Updated successfully !";
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry some error occured.';
        }
        $this->setResponse($result);
    }

    public function getSubServiceList($serviceId)
    {
        $where = [
            'service_status' => 1,
            'parentId' => $serviceId,
        ];
        $serviceList = $this->apiFunctions->getDataFromDb('*', 'service_list', $where);
        if (!empty($serviceList)) {
            $result['success'] = true;
            $result['message'] = $serviceList;
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry No Sub Service found.';
        }
        $this->setResponse($result);
    }

    public function createService($data) {
        $checkFor = ['service_name','service_short_description','service_long_description','service_price_type','service_price'];
        $this->checkForRequired($checkFor, $data);

        $insertData = $this->apiFunctions->addDataToDb('service_list', $data);
        if ($insertData) {
            $result['success'] = true;
            $result['message'] = "Service ".$data['service_name']." is created";
        } else {
            $result['success'] = false;
            $result['message'] = "An unknown error occured in adding service";
        }
        $this->setResponse($result);
    }

    public function addToCart() {
        $productId = isset($_REQUEST['product']) ? $_REQUEST['product'] : "";
        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "add";
        $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";

        $dataList = $this->apiFunctions->getDataFromDb('*', 'products', ['id' => $productId]);
        if (empty($dataList)){
            $result['success'] = false;
            $result['message'] = "Product not found";
            $this->setResponse($result);
        }

        if ($type == "add") {
            $addToDB = $this->apiFunctions->addDataToDb('user_cart', ['user_id' => $user,'product_id' => $productId]);
            $result['success'] = true;
            $result['message'] = "Product added to cart";    
        } else {
            $addToDB = $this->apiFunctions->removeDataToDb('user_cart', ['user_id' => $user,'product_id' => $productId]);
            $result['success'] = true;
            $result['message'] = "Product removed from cart";    
        }
        
        $this->setResponse($result);
    }

    public function makeServiceActive($serviceId, $activeStatus) {
        $serviceMessage = ($activeStatus) ? "Active" : "InActive";
        $op = $this->apiFunctions->updateDataToDb('service_list', ['service_status' => $activeStatus], ['id' => $serviceId]);

        if ($op) {
            $result['success'] = true;
            $result['message'] = "Service is now ". $serviceMessage;
        } else {
            $result['success'] = false;
            $result['message'] = "An unknown error occured";
        }

        $this->setResponse($result);
    }
}