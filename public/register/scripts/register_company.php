<?php
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
		die("Missing data");	

	require_once "../../../includes/initialize.php";

	$country_id = $_POST['countries'];
	$company_name = $_POST['company_name'];
	$branch = $_POST['branch'];
	
	$errors = array();
	if(!$session->is_logged_in())
		exitScript("Please log in first", "failed");
	$email = $session->user_id;

	if(!validate())
		exitScript("", "failed");

	$sql = "INSERT INTO `profile`(`country_id`, `company_name`, `email`, `branch_id`) ";
	$sql .= "VALUES('{$country_id}', '{$company_name}', '{$email}', '{$branch}');";

	$sql .= "INSERT INTO `facebook`(`country_id`, `company_name`) VALUES ('{$country_id}', '{$company_name}');";
	$sql .= "INSERT INTO `maps`(`country_id`, `company_name`) VALUES ('{$country_id}', '{$company_name}');";
	$sql .= "INSERT INTO `statistics`(`country_id`, `company_name`) VALUES ('{$country_id}', '{$company_name}');";
	$sql .= "INSERT INTO `manage`(`country_id`, `company_name`, `email`) VALUES('{$country_id}', '{$company_name}', '{$email}')";

	$result = $db->multi_query($sql);
	if(!$result)
		exitWithError("HTTP/1.1 500 Internal Server Error", "Something went wrong. Try again later");

	if($db->affected_rows() == 0)
		exitScript("Profile registration failed", "failed");

	$path = PROFILE_DIR."{$country_id}_{$company_name}".DS.'images'.DS;
	mkdir($path, 0777, true);
	
	exitScript("Profile registered successfully", "success");

	
	function exitScript($message, $status)
	{
		global $errors;
		array_push($errors, $message);
		die(json_encode(array("errors"=>$errors, "status"=>$status)));
	}

	/// TO DO: Da se implementira
	function validate()
	{
		global $country_id;
		global $company_name;
		return true;
	}
?>