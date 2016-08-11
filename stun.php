<?php
	$result = file_get_contents("https://computeengineondemand.appspot.com/turn?username=41784574&key=4080218913");
	die(json_encode($result));
?>