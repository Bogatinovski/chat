<?php
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
		die("Error");	

	require_once "../../includes/initialize.php";

	$unique_id = $_GET['id'];
	$offset = $_GET['offset'];
	$offset *= 10;

	$sql = "SELECT `first_name`, `last_name` FROM `users` WHERE `unique_id` = ? LIMIT 1";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $unique_id);
	$stmt->execute();
	$stmt->bind_result($first_name, $last_name);

	$return = array("user"=>array(), "messages"=>array());

	while($stmt->fetch())
	{
		$return["user"]["name"] = $first_name;
		$return["user"]["last"] = $last_name;
	}
	$stmt->close();

	$sql = "SELECT `sender`, `message`, `time_created`, `message_id` FROM `messages` ";
	$sql .= "WHERE (`sender`= ? AND `receiver` = ?) OR (`sender`=? AND `receiver` = ?) ";
	$sql .= "ORDER BY `time_created` DESC, `message_id` DESC LIMIT 10 OFFSET ?";
	//$sql .= "ORDER BY `time_created` DESC, `message_id` DESC";

	$stmt = $db->prepare($sql);
	
	$stmt->bind_param('iiiii', $session->unique_id, $unique_id, $unique_id, $session->unique_id, $offset);
	$stmt->execute();
	$stmt->bind_result($sender, $message, $time_created, $message_id);

	while($stmt->fetch())
	{
		array_push($return['messages'], array("id"=>$message_id, "sender"=>$sender, "message"=>$message, "time"=>$time_created));
	}
		
	
	die(json_encode($return));
?>