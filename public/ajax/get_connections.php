<?php
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
		die("Error");	

	require_once "../../includes/initialize.php";

	$sql = "SELECT u.`unique_id`, u.`first_name`, u.`last_name` ";
	$sql .= "FROM `connections` c, `users` u WHERE u.`unique_id`=c.`user2` AND c.`user1` = ?";

	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $session->unique_id);
	$stmt->execute();
	$stmt->bind_result($unique_id, $first_name, $last_name);

	$return = array();
	while($stmt->fetch())
		array_push($return, array("id"=>$unique_id, "name"=>$first_name, "last"=>$last_name));
	
	die(json_encode($return));
?>