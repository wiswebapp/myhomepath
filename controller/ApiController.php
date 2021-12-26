<?php
namespace controller;

use modal\ApiFunctions;

class ApiController {

    public $ApiFunctions;

	public function __construct(){
        $this->apiFunctions = new ApiFunctions();
    }

    public function isAuthorized($request) {
        $unAuthoriedCalls = ['login', 'register', 'forgetPassword', 'loginAdmin'];
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
            //Generate access token
            $this->apiFunctions->createAccessToken($registerUser);
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

    public function loginAdmin(){
        $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "";
        $phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : "";
        $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : "";
        $isSuccess = false;
        $responseMsg = "You are not registerd";

        $userExist = $this->apiFunctions->checkUserExist($email, $phone, 'Admin');
        if($userExist) {
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

    public function addProduct() {
        $data['user_id'] = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
        $data['category_id'] = isset($_REQUEST['categoryId']) ? $_REQUEST['categoryId'] : "";
        $data['product_name'] = isset($_REQUEST['productName']) ? $_REQUEST['productName'] : "";
        $data['product_description'] = isset($_REQUEST['description']) ? $_REQUEST['description'] : "";
        $data['price'] = isset($_REQUEST['price']) ? $_REQUEST['price'] : "";
        $data['availblity'] = isset($_REQUEST['availblity']) ? $_REQUEST['availblity'] : "Yes";
        //$data['productImage'] = isset($_REQUEST['productImage']) ? $_REQUEST['productImage'] : "";

        $isAdmin = $this->apiFunctions->isAdmin($data['user_id']);
        if(! $isAdmin) {
            $returnArr['success'] = false;
            $returnArr['message'] = 'You are not authorized to perform this action';
            $this->setResponse($returnArr);
        }

        if (empty($data['category_id']) || empty($data['product_name']) || empty($data['product_description']) || empty($data['price']) ) {
            $returnArr['success'] = false;
            $returnArr['message'] = "Required parameter for product add missing .!";
            $this->setResponse($returnArr);
        }

        $addProduct = $this->apiFunctions->addDataToDb('products', $data);
        if($addProduct) {
            $returnArr['success'] = true;
            $returnArr['message'] = "Product Added successfully .!";
            $returnArr['data'] = $this->apiFunctions->getDataFromDb('*','products',['id' => $addProduct]);
        } else {
            $returnArr['success'] = false;
            $returnArr['message'] = "Failed to add product .!";
        }

        $this->setResponse($returnArr);
    }

    public function getProductList() {
        $where = [
            'status' => 'Active',
            'deleted_at IS' => null,
            'availblity' => 'Yes',
        ];
        $dataList = $this->apiFunctions->getProductList();
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

    public function getSingleProduct(){
        $productId = isset($_REQUEST['product']) ? $_REQUEST['product'] : "";
        
        $product = $this->apiFunctions->getProductList($productId);
        if (! empty($product)){
            $result['success'] = true;
            $result['data'] = $product[0];
            $result['message'] = "1 product Found";
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry No product found.';
        }
        $this->setResponse($result);
    }

    public function addAddress(){
        $data['phone_no'] = isset($_REQUEST['phoneNo']) ? $_REQUEST['phoneNo'] : "";
        $data['address'] = isset($_REQUEST['address']) ? $_REQUEST['address'] : "";
        $data['locality'] = isset($_REQUEST['locality']) ? $_REQUEST['locality'] : "";
        $data['landmark'] = isset($_REQUEST['landmark']) ? $_REQUEST['landmark'] : "";
        $data['city'] = isset($_REQUEST['city']) ? $_REQUEST['city'] : "";
        $data['pincode'] = isset($_REQUEST['pincode']) ? $_REQUEST['pincode'] : "";
        $data['address_type'] = isset($_REQUEST['address_type']) ? $_REQUEST['address_type'] : "";
        $data['user_id'] = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";

        if( empty($data['user_id']) 
            || empty($data['address']) 
            || empty($data['locality']) 
            || empty($data['city']) 
            || empty($data['pincode']))
        {
            $result['success'] = false;
            $result['message'] = 'Sorry all fields required';
            $this->setResponse($result);
        }
        
        $addToDB = $this->apiFunctions->addDataToDb('user_address', $data);
        if (! empty($addToDB)){
            $result['success'] = true;
            $result['message'] = "Address Added successfully !";
            $result['data'] = $catList = $this->apiFunctions->getDataFromDb('*', 'user_address', ['id' => $addToDB]);
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry Failed to address.';
        }
        $this->setResponse($result);
    }

    public function updateAddress(){
        $addressId = isset($_REQUEST['addressId']) ? $_REQUEST['addressId'] : "";
        $data['user_id'] = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
        if( empty($data['user_id']) || empty($addressId)) {
            $result['success'] = false;
            $result['message'] = 'Sorry all fields required.';
            $this->setResponse($result);
        }
        $oldAddress = $this->apiFunctions->getDataFromDb('*', 'user_address', ['id' => $addressId]);
        $data['phone_no'] = isset($_REQUEST['phoneNo']) ? $_REQUEST['phoneNo'] : $oldAddress[0]['phone_no'];
        $data['address'] = isset($_REQUEST['address']) ? $_REQUEST['address'] : $oldAddress[0]['address'];
        $data['locality'] = isset($_REQUEST['locality']) ? $_REQUEST['locality'] : $oldAddress[0]['locality'];
        $data['landmark'] = isset($_REQUEST['landmark']) ? $_REQUEST['landmark'] : $oldAddress[0]['landmark'];
        $data['city'] = isset($_REQUEST['city']) ? $_REQUEST['city'] : $oldAddress[0]['city'];
        $data['pincode'] = isset($_REQUEST['pincode']) ? $_REQUEST['pincode'] : $oldAddress[0]['pincode'];
        $data['address_type'] = isset($_REQUEST['address_type']) ? $_REQUEST['address_type'] : $oldAddress[0]['address_type'];

        
        
        $addToDB = $this->apiFunctions->updateDataToDb('user_address', $data, ['id' => $addressId]);
        if (! empty($addToDB)){
            $result['success'] = true;
            $result['message'] = "Address Updated successfully !";
            $result['data'] = $catList = $oldAddress;
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry Failed to address.';
        }
        $this->setResponse($result);
    }

    public function listAddress()
    {
        $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";

        $addressData = $this->apiFunctions->getDataFromDb('*', 'user_address', [
            'user_id' => $user, 
            'status' => 'Active'
        ]);
        if (! empty($addressData)){
            $result['success'] = true;
            $result['data'] = $addressData;
            $result['message'] = "1 product Found";
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry No Address found.';
        }
        $this->setResponse($result);
    }

    public function deleteAddress(){
        $addressId = isset($_REQUEST['addressId']) ? $_REQUEST['addressId'] : "";
        $user_id = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
        if( empty($user_id) || empty($addressId)) {
            $result['success'] = false;
            $result['message'] = 'Sorry all fields required.';
            $this->setResponse($result);
        }

        $addressData = $this->apiFunctions->getDataFromDb('*', 'user_address', ['user_id' => $user_id,'id' => $addressId]);
        if (! empty($addressData)){
            $remove = $this->apiFunctions->removeDataToDb('user_address', ['user_id' => $user_id, 'id' => $addressId]);
            if($remove) {
                $result['success'] = true;
                $result['message'] = "Address removed successfully.";
            } else {
                $result['success'] = true;
                $result['message'] = "Unknown Error Occured .!";
            }
        } else {
            $result['success'] = false;
            $result['message'] = 'Sorry No Address found.';
        }
        $this->setResponse($result);
    }

    public function placeOrder(){
        $grandTotal = 0;
        $orderId = "ORDER".date('dmyhis') . "_" .rand(10,99);
        $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
        $user_address = isset($_REQUEST['user_address']) ? $_REQUEST['user_address'] : "";
        $productArr = isset($_REQUEST['product']) ? json_decode($_REQUEST['product'], TRUE) : "";
        
        if( empty($productArr) || empty($user_address)) {
            $result['success'] = true;
            $result['message'] = "Unknown Error Occured .!";
            $this->setResponse($result);
        }

        $orderDetailStatus = true;
        foreach($productArr as $product) {
            $productId = $product['id'];

            $addressData = $this->apiFunctions->getDataFromDb('*', 'user_address', ['id' => $user_address]);
            $getProduct = $this->apiFunctions->getDataFromDb('*', 'products', [
                'id' => $productId, 
                'availblity' => 'Yes',
                'status' => 'Active',
            ]);

            if( empty($getProduct)) {
                $result['success'] = true;
                $result['message'] = "Unknown Error Occured .!";
                $this->setResponse($result);
            }

            $grandTotal = $grandTotal + $product['price'];
            //Add data to order details
            $orderArr['order_id'] = $orderId;
            $orderArr['category_id'] = $product['category_id'];
            $orderArr['product_id'] = $product['id'];
            $orderArr['product_name'] = $product['product_name'];
            $orderArr['product_price'] = $product['price'];
            $orderArr['quantity'] = 1;
            $orderArr['order_price'] = ($orderArr['product_price'] * $orderArr['quantity']);
            $orderArr['order_address'] = json_encode($addressData[0]);
            $insertData = $this->apiFunctions->addDataToDb('order_details', $orderArr);
            if(! $insertData) {
                $orderDetailStatus = false;
            }
        }


        if($orderDetailStatus) {
            //Generate Order Array
            $orderTblArr['order_id'] = $orderId;
            $orderTblArr['user_id'] = $user;
            $orderTblArr['user_address'] = $user_address;
            $orderTblArr['order_status'] = 1;
            $orderTblArr['grand_total'] = $grandTotal;
            $placeOrder = $this->apiFunctions->addDataToDb('orders', $orderTblArr);
            if($placeOrder) {
                //Empty Cart
                $emptyCart = $this->apiFunctions->removeDataToDb('user_cart', ['user_id' => $user]);
                if ($emptyCart) {
                    $result['success'] = true;
                    $result['message'] = "Order Placed successfully";
                    $this->setResponse($result);
                }
            }
        } else {
            $result['success'] = true;
            $result['message'] = "Unknown Error Occured while placing order .!";
            $this->setResponse($result);
        }
    }

    public function listOrder(){
        $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
        $flag = isset($_REQUEST['flag']) ? $_REQUEST['flag'] : 0;
        
        $orderData = $this->apiFunctions->getListOfOrder($user, $flag);
        if(! empty($orderData)) {
            $result['success'] = true;
            $result['message'] = count($orderData) . " Order found";
            $result['data'] = $orderData;
        } else {
            $result['success'] = false;
            $result['message'] = "No Order Data found";
        }
        
        $this->setResponse($result);
    }

    public function listCart(){
        $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
        
        $where = ['user_id' => $user];
        $cartList = $this->apiFunctions->getDataFromDb('*', 'user_cart', $where);
        if(! empty($cartList)) {
            $i=0;
            foreach($cartList as $cartItem) {
                $product = $cartItem['product_id'];
                $field = "id,category_id,product_name,price";
                $productData = $this->apiFunctions->getDataFromDb($field, 'products', ['id' => $product]);
                $cartList[$i++] = $productData[0]; 
            }
            //echo "<pre>";print_r($cartList);exit;
            $result['success'] = true;
            $result['message'] = count($cartList) . ' product found in cart';
            $result['data'] = $cartList;
        } else {
            $result['success'] = false;
            $result['message'] = 'Your cart is empty';
            $result['data'] = [];
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

    public function changePassword(){
        $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : "";
        $oldPassword = isset($_REQUEST['oldPassword']) ? $_REQUEST['oldPassword'] : "";
        $newPassword = isset($_REQUEST['newPassword']) ? $_REQUEST['newPassword'] : "";
        $confirmPassword = isset($_REQUEST['confirmPassword']) ? $_REQUEST['confirmPassword'] : "";

        if($newPassword != $confirmPassword) {
            $result['success'] = false;
            $result['message'] = "Confirm password failed to match.";
            $this->setResponse($result);
        }

        $uData = $this->apiFunctions->getDataFromDb('id', 'med_users', ['password' => MD5($oldPassword),'id' => $user]);
        if( empty($uData)) {
            $result['success'] = false;
            $result['message'] = "Old Password is wrong.";
            $this->setResponse($result);
        }

        $updatedData = $this->apiFunctions->updateDataToDb('med_users', ['password' => MD5($newPassword)], ['id' => $user]);
        $result['success'] = true;
        $result['message'] = "Password Changed successfully .!";
        $this->setResponse($result);
    }

    public function forgetPassword(){
        $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : "";
        $phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : "";

        if( empty($email) && empty($phone) ) {
            $result['success'] = false;
            $result['message'] = "Required parameter missing";
            $this->setResponse($result);
        }
        
        if (! empty($email)) {
            $uData = $this->apiFunctions->getDataFromDb('id', 'med_users', ['email' => $email,'isActive' => 'Yes']);
        } elseif (! empty($phone)) {
            $uData = $this->apiFunctions->getDataFromDb('id', 'med_users', ['phone' => $phone,'isActive' => 'Yes']);
        }

        if ( empty($uData)) {
            $result['success'] = false;
            $result['message'] = "No User Found with this email.";
            $this->setResponse($result);
        }

        $result['success'] = true;
        $result['message'] = "Reset Password sent successfully .!";
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