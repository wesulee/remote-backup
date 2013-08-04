<?php
// all calls to API (by client) should be done through this file

require_once('../includes/db.php');
require_once('api.class.php');
session_start();

$action = $_POST['action'];

if (empty($action)) {
	echo 'no action';
	exit();
}

if ($action == 'authenticate') {
	$response = array('action' => 'authenticate');
	$db = new DB();
	if ($db->validLogin($_POST['username'], $_POST['password'])) {
		$_SESSION['auth'] = 1; 		
		$_SESSION['username'] = $_POST['username'];
		echo json_encode($response);
		exit();
	}
	else {
		$response['error'] = 'Invalid credentials';
		echo json_encode($response);
		exit();
	}
}
elseif ($_SESSION['auth'] != 1) {
	echo json_encode(array('action' => $action, 'error' => 'Invalid credentials'));
	exit();

}


$API = new API($_SESSION['username']);

switch ($action)
{
	case 'uploadfile':
		echo $API->uploadfile($_FILES, $_POST);
		break;
	case 'multiuploadfile':
		echo $API->multiuploadfile($_FILES, $_POST);
		break;
	case 'downloadfile':
		echo $API->downloadfile($_POST);
		break;
	default:
		// unknown action
}