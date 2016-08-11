<?php
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
		die("Error");	

	require_once "../../includes/initialize.php";
	$user = $_GET['user'];

	$sql = "SELECT `first_name`, `last_name`, `unique_id` FROM `users` WHERE `unique_id` = ? LIMIT 1";

	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $user);
	$stmt->execute();
	$stmt->bind_result($first_name, $last_name, $unique_id);
	$stmt->fetch();
	$stmt->close();

	$return = array("id"=>$unique_id, "name"=>$first_name, "last"=>$last_name);
	die(json_encode($return));
?>