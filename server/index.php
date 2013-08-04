<?php
require_once('includes/db.php');
session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Remote Backup</title>
</head>

<body>
<h3>Login</h3>
<form action="index.php" method="post">
	Username <input name="username" type="text" value="" /><br>
	Password <input name="password" type="password" value="" /><br>
	<input name="Submit" type="submit" />
</form>
<p><a href="register.php">Register here</a></p>
<?php
// check if logged in
if ($_SESSION['auth'] === 1) {
	echo "You are logged in as {$_SESSION['username']}";
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$db = new DB();
	if ($db->validLogin($_POST['username'], $_POST['password'])) {
		// set session stuff here
		echo 'Valid user';
	}
	else {
		echo 'Wrong login information';
	}
}

?>
</body>
</html>
