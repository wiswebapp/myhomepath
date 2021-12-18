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

	case 'getCategory':
		$apiController->getCategoryList();
		break;

	case 'addCategory':
		$apiController->addCategoryList();
		break;

	case 'getProduct':
		$apiController->getProductList();
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

	default:
		$apiController->sessionOut('Required parameter missing');
		break;
}
