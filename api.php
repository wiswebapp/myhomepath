<?php
include_once(__DIR__ . '/vendor/autoload.php');

use controller\ApiController;

$request = isset($_REQUEST['request']) ? trim($_REQUEST['request']) : "";
$apiController = new ApiController();

$apiController->isAuthorized($request);

switch ($request) {
	case 'register':
		$apiController->register();
		break;

	case 'login':
		$apiController->login();
		break;

	case 'loginAdmin':
		$apiController->loginAdmin();
		break;

	case 'getCategory':
		$apiController->getCategoryList();
		break;

	case 'addCategory':
		$apiController->addCategoryList();
		break;

	case 'addProduct':
		$apiController->addProduct();
		break;

	case 'getProduct':
		$apiController->getProductList();
		break;

	case 'getSingleProduct':
		$apiController->getSingleProduct();
		break;

	case 'listCart':
		$apiController->listCart();
		break;

	case 'addToCart':
		$apiController->addToCart();
		break;

	case 'editCategory':
		$apiController->editCategoryList();
		break;

	case 'listAddress':
		$apiController->listAddress();
		break;

	case 'addAddress':
		$apiController->addAddress();
		break;

	case 'updateAddress':
		$apiController->updateAddress();
		break;

	case 'deleteAddress':
		$apiController->deleteAddress();
		break;

	case 'placeOrder':
		$apiController->placeOrder();
		break;
	
	case 'acceptOrder':
		$apiController->acceptOrder();
		break;
	
	case 'declineOrder':
		$apiController->declineOrder();
		break;

	case 'listOrder':
		$apiController->listOrder();
		break;

	case 'adminListOrder':
		$apiController->listOrder(true);
		break;

	case 'adminOrderList':
		$apiController->adminOrderList();
		break;

	case 'changePassword':
		$apiController->changePassword();
		break;

	case 'forgetPassword':
		$apiController->forgetPassword();
		break;

	default:
		$apiController->sessionOut('Required parameter missing');
		break;
}
