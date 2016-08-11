<?php
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
		die("Error");	

	require_once "../../includes/initialize.php";

	$receiver = $_POST['to'];
	$message = $_POST['msg'];

	$sql = "INSERT INTO `messages`(`sender`, `receiver`, `message`) VALUES(?, ?, ?)";

	$stmt = $db->prepare($sql);
	$stmt->bind_param('iis', $session->unique_id, $receiver, $message);
	$stmt->execute();
	$stmt->close();


	$sql = "SELECT `message_id`, `message`, `time_created` ";
	$sql .= "FROM `messages` WHERE `sender` = ? AND `receiver` = ? ORDER BY `time_created` DESC, `message_id` DESC LIMIT 1";

	$stmt = $db->prepare($sql);
	$stmt->bind_param('ii', $session->unique_id, $receiver);
	$stmt->execute();
	$stmt->bind_result($message_id, $message, $time_created);
	$stmt->fetch();
	$stmt->close();

	$return = array("id"=>$message_id, "msg"=>$message, "time"=>$time_created);
	
	die(json_encode($return));
?>