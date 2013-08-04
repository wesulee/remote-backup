<?php
// execute this file to create the proper database tables

require('../includes/db_variables.php'); 	// database credentials
require('db_setup_variables.php');			// table settings

// attempt to connect to database
try {
	$dbh = new PDO("{$DB_TYPE}:host={$DB_HOST};dbname={$DB_NAME}", $DB_USER, $DB_PASS);
}
catch (PDOException $e) {
	echo 'Error! '.$e->getMessage();
	die();
}

// check if users table exist
$query = $dbh->query("SHOW TABLES LIKE 'users'");
$query->execute();
if ($query->rowCount()==0) {
	// table users needs to be created
	$sql = "CREATE TABLE `users` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `login` varchar({$GLOBALS['users_login']}) NOT NULL,
 `password` varchar({$GLOBALS['users_password']}) NOT NULL,
 `email` varchar({$GLOBALS['users_email']}) DEFAULT NULL,
 `joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`),
 UNIQUE KEY `login` (`login`)
)";
	$stmt = $dbh->prepare($sql);
	$result = $stmt->execute();
	if ($result) {
		echo "users table successfully created.\n";
	}
	else {
		echo "Unable to create users table.\n";
		$errors = true;
	}
}

if (!isset($errors)) {
	echo 'Everything looks good!';
}
elseif ($errors) {
	echo 'Errors occurred. Database not ready.';
}

?>