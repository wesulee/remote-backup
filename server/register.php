<?php
session_start();
// redirect to index if already logged in
if ($_SESSION['auth'] == 1) {
	header('Location: index.php');
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Registration</title>
</head>
<body>
<?php
require_once('includes/user_variables.php');

if (!$allowRegister) {
echo '<p>Sorry... registration is currently not allowed.</p>
</body>
</html>';

} 
else {
	$emailFormStr = $emailOptional? 'optional ' : '';
	if (isset($_SESSION['register_error'])) {
		echo '<b>'.$_SESSION['register_error'].'</b>';
		unset($_SESSION['register_error']);
	}
	echo '<form action="register_submit.php" method="post">
Username <input name="username" type="text" value="'.$_SESSION['username'].'" /><br />
Password <input name="password" type="password" /><br />
Email '.$emailFormStr.'<input name="email" type="text" value="'.$_SESSION['email'].'"/><br />
<input name="Submit" type="submit" />
</form>
</body>
</html>';
}

?>