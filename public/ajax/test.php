<?php
	require_once "../../includes/initialize.php";

	$sql = "INSERT INTO `messages`(`sender`, `receiver`, `message`) VALUES(?, ?, ?)";

	$stmt = $db->prepare($sql);

	$message = "Ova e test";
	$i=0;
	$i1000 = 1000;
	$i1005 = 1005;

	while(true)
	{
		if($i %2 == 0)
			$stmt->bind_param('iis', $i1000, $i1005, $message);
		else
			$stmt->bind_param('iis', $i1005, $i1000, $message);
		$i++;
		$stmt->execute();
		echo $i;
	}
	
	$stmt->close();
?>