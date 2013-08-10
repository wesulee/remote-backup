<?php
require_once('includes/db.php');
require_once('includes/helper_functions.php');
session_start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Remote Backup</title>
</head>
<body>
<?php
if ($_SESSION['auth'] === 1) { ?>
	<p>You are logged in as <?php echo $_SESSION['username'] ?></p>
	<p><a href="browse.php">Browse files</a></p>
<?php 
}
else { ?>
<h3>Login</h3>
<form action="login.php" method="post">
	Username <input name="username" type="text" value="" /><br>
	Password <input name="password" type="password" value="" /><br>
	<input name="Submit" type="submit" />
</form>
<p><a href="register.php">Register here</a></p>
<?php } ?>
</body>
</html>
