<?php
require_once('includes/user_variables.php');
require_once('includes/db.php');
session_start();

// redirect to index if already logged in
if ($_SESSION['auth'] === 1) {
	header('Location: index.php');
	exit();
}

// validates username, returns NULL if acceptable username
function checkUsernameError($username)
{
	if (is_null($username)) {
		return 'Invalid username';
	}
	// check for invalid characters
	foreach ($username as $c) {
		if (mb_strpos($GLOBALS['usernameChar'], $c) === false) {
			return 'Invalid username';
		}
	}

	// get length settings
	$minUsername = is_null($GLOBALS['minUsername']) ? 1 : $GLOBALS['minUsername'];
	$maxUsername = is_null($GLOBALS['maxUsername']) ? $GLOBALS['users_login'] : $GLOBALS['maxUsername'];

	$length = mb_strlen($username);
	if ($length < $minUsername) {
		return 'Username cannot be less than '.$minUsername.' characters';
	}
	if ($length > $maxUsername) {
		return 'Username cannot be greater than '.$maxUsername.' characters';
	}

	$db = new DB();
	if ($db->userExists($username)) {
		return 'Username already exists';
	}
}

// validates password, returns NULL is acceptable password
function checkPasswordError($password)
{
	if (is_null($password)) {
		return 'Invalid password';
	}
	// get length settings
	$minPassword = is_null($GLOBALS['minPassword']) ? 6 : $GLOBALS['minPassword'];
	$maxPassword = is_null($GLOBALS['maxPassword']) ? $GLOBALS['users_password'] : $GLOBALS['maxPassword'];

	$length = mb_strlen($password);
	if ($length < $minPassword) {
		return 'Password cannot be less than '.$minPassword.' characters';
	}
	if ($length > $maxPassword) {
		return 'Password cannot be greater than '.$maxPassword.' characters';
	}
}

// validates email, returns NULL is acceptable email
function checkEmailError($email, $allowEmpty)
{
	if ($allowEmpty && empty($email)) {
		return NULL;
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return 'Invalid email';
	}
}


// immediately redirect if not allowed or register or request wasn't POST
if (!$allowRegister || $_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: index.php');
	exit();
}

// store info in session, so able to resume registration if error
$_SESSION['username'] = $_POST['username'];
$_SESSION['email'] = $_POST['email'];

// validate registration information
$errors = array(
	checkUsernameError($_POST['username']),
	checkPasswordError($_POST['password']),
	checkEmailError($_POST['email'], $emailOptional)
	);
foreach ($errors as $error) {
	if (!is_null($error)) {
		$_SESSION['register_error'] = $error;
		header('Location: register.php');
		exit();
	}
}

$db = new DB();
if ($db->registerUser($_POST['username'], $_POST['password'],
	$emailOptional && empty($_POST['email']) ? NULL : $_POST['email'])) {
	// registration successful
	$_SESSION['register_notice'] = 'User successfully registered! Please login.';
	header('Location: index.php');
}
else {
	// database error
	$_SESSION['register_error'] = 'An unexpected error has occurred. Please try again.';
	header('Location: register.php');
	exit();
}


?>