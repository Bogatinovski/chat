<?php
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
		die("Missing data");	

	require_once "../../includes/initialize.php";

	$email = $_POST['email'];
	$first = $_POST['first'];
	$last = $_POST['last'];
	$pass = $_POST['pass'];
	$conf = $_POST['confPass'];
	$errors = array();

	if(!validate())
		die(json_encode(array("errors"=>$errors, "status"=>"failed")));

	$sql = "SELECT `email` FROM `users` WHERE `email`='{$email}' LIMIT 1";
	$result = $db->query($sql);
	if(!$result)
		exitWithError("HTTP/1.1 500 Internal Server Error", "Something went wrong. Registration failed");

	if($db->affected_rows() > 0)
		exitScript($email." is already in use.", "failed");

	$encripted = password_hash($pass, PASSWORD_BCRYPT, ['cost'=>10]);
	$sql = "INSERT INTO `users`(`email`, `first_name`, `last_name`, `hpass`) ";
	$sql .= "VALUES('{$email}', '{$first}', '{$last}', '{$encripted}');";
	$result = $db->query($sql);
	if($db->affected_rows() == 0)
		exitScript("Registration failed", "failed");

	//$path = ACCOUNT_DIR.$email.DS.'images'.DS;
	//mkdir($path, 0777, true);

	exitScript("Successfully registered.", "success");

	function exitScript($message, $status)
	{
		global $errors;
		array_push($errors, $message);
		die(json_encode(array("errors"=>$errors, "status"=>$status)));
	}

	function validate()
	{
		global $email;
		global $first;
		global $last;
		global $pass;
		global $conf;
		global $errors;
		$result = true;

		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			array_push($errors, "Invalid email format");
			$result = false;
		}
		if(strlen($first) == 0)
		{
			array_push($errors, "You need to enter your first name");
			$result = false;
		}
		if(strlen($last) == 0)
		{
			array_push($errors, "You need to enter your last name");
			$result = false;
		}
		if(strlen($pass) < 8)
		{
			array_push($errors, "Password length should be at least 8 characters");
			$result = false;
		}
		if($pass != $conf)
		{
			array_push($errors, "Passwords don't match");
			$result = false;
		}
		return $result;
	}
?>