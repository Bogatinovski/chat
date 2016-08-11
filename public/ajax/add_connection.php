<?php
	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
		die("Error");	

	require_once "../../includes/initialize.php";

	$email = $_POST['email'];

	$sql = "SELECT `email` FROM `users` WHERE `email` = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s', $email);
	$stmt->execute();
	$stmt->bind_result($from_db);
	$stmt->fetch();
	$stmt->close();

	if(!$from_db || $from_db != $email)
	{
		$return = array("status"=>"failed", "message"=>"A user with that email doesn't exist");
		die(json_encode($return));
	}

	if($email == $session->email)
	{
		$return = array("status"=>"failed", "message"=>"You can't add yourself");
		die(json_encode($return));
	}

	$sql = "SELECT `user1` FROM `connections` WHERE `user1`= ? AND `user2`= (SELECT `unique_id` FROM `users` WHERE `email`=?)";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('is', $session->unique_id, $from_db);
	$stmt->execute();
	$stmt->bind_result($user_db);
	$stmt->fetch();
	$stmt->close();

	if($user_db)
	{
		$return = array("status"=>"failed", "message"=>"You are already connected to " . $email);
		die(json_encode($return));
	}

	$sql = "INSERT INTO `connections`(`user1`, `user2`) VALUES(?, (SELECT `unique_id` FROM `users` WHERE `email`=?)), ";
	$sql .= "((SELECT `unique_id` FROM `users` WHERE `email`=?), ?)";

	$stmt = $db->prepare($sql);
	$stmt->bind_param("issi", $session->unique_id, $email, $email, $session->unique_id);
	$stmt->execute();
	
	if($stmt->affected_rows == -1)
	{
		$return = array("status"=>"failed", "message"=>"Connecting failed, try again later");
		die(json_encode($return));
	}



	$stmt->close();

	$return = array("status"=>"success", "message"=>"You are now connected with " . $email);
	die(json_encode($return));
?>