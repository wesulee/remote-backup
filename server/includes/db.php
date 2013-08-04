<?php
require_once('db_variables.php');

class DB
{
	private $dbh;

	public function __construct()
	{
		$new_con = "{$GLOBALS['DB_TYPE']}:host={$GLOBALS['DB_HOST']};dbname={$GLOBALS['DB_NAME']}";
		try {
			$this->dbh = new PDO($new_con, $GLOBALS['DB_USER'], $GLOBALS['DB_PASS']);
		}
		catch (PDOException $e) {
			echo 'Error! '.$e->getMessage();
			die();
		}
	}

	// return PDORow of user if provided valid login credentials, else false
	public function validLogin($login, $password)
	{
		$userInfo = $this->userInfo($login);
		if (!$userInfo) {
			// user doesn't exist
			return false;
		}

		require_once($GLOBALS['passwordLibrary']);
		if (password_verify($password, $userInfo['password'])) {
			return $userInfo;
		}
		else {
			return false;
		}
	}

	// returns PDORow of user info if exists in database, else false
	// can query user by login or id
	public function userInfo($login = NULL, $id = NULL)
	{
		if (!is_null($login)) {
			$query = $this->dbh->prepare("SELECT * FROM users WHERE login = :login LIMIT 1");
			$query->bindParam(':login', $login, PDO::PARAM_STR);
		}
		elseif (!is_null($id)) {
			$query = $this->dbh->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
			$query->bindParam(':id', $id, PDO::PARAM_INT);
		}
		else {
			trigger_error("$login and $id can't both be null");
			return false;
		}

		$result = $query->execute();
		if (!$result) {
			trigger_error("query was unsuccessful");
			return false;
		}
		return $query->fetch(PDO::FETCH_LAZY);
	}

	// returns true if user exists, else false
	public function userExists($login)
	{
		$query = $this->dbh->prepare("SELECT EXISTS(SELECT 1 FROM users WHERE login = :login)");
		$query->bindParam(':login', $login, PDO::PARAM_STR);

		$result = $query->execute();
		if (!$result) {
			trigger_error("query was unsuccessful");
			return true; 	// assume user exists if database query fails
		}

		$exists = $query->fetch(PDO::FETCH_NUM)[0];
		return $exists === '1' ? true : false;
	}

	// returns id of new user if able to successfully register, else false
	public function registerUser($login, $password, $email)
	{
		$userInfo = $this->userInfo($login);
		if ($userInfo) {
			return false; 	// user already exists
		}

		require_once($GLOBALS['passwordLibrary']);
		$hashed = password_hash($password, PASSWORD_BCRYPT, array('cost' => $GLOBALS['passwordCost']));

		$query = $this->dbh->prepare("INSERT INTO users (login, password, email) 
			VALUES (:login, :password, :email)");
		$query->bindParam(':login', $login, PDO::PARAM_STR);
		$query->bindParam(':password', $hashed, PDO::PARAM_STR);
		$query->bindParam(':email', $email, PDO::PARAM_STR);

		$result = $query->execute();
		if (!$result) {
			trigger_error("query was unsuccessful");
			return false;
		}
		return $this->dbh->lastInsertId();
	}
}