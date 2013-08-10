<?php
require_once('includes/db.php');
require_once('includes/helper_functions.php');
require_once('includes/path_variables.php');
session_start();

if ($_SESSION['auth'] === 1) {
	redirect('index.php');
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$db = new DB();
	if ($db->validLogin($_POST['username'], $_POST['password'])) {
		$_SESSION = array(); 		// reset all session data
		$_SESSION['type'] = 'b'; 	// browser session
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['auth'] = 1;
		$_SESSION['user_path'] = joinPath($userDir, $_POST['username']);
		redirect('index.php');
	}
	else {
		$_SESSION['login_error'] = 'Invalid login';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
</head>
<body>
<h3>Login</h3>
<?php
if (isset($_SESSION['login_error'])) {
	echo '<b>'.$_SESSION['login_error'].'</b>';
	unset($_SESSION['login_error']);
}
?>
<form action="login.php" method="post">
	Username <input name="username" type="text" value="" /><br>
	Password <input name="password" type="password" value="" /><br>
	<input name="Submit" type="submit" />
</form>
</body>
</html>