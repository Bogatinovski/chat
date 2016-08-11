<?php
	require_once "../../../includes/initialize.php";

	$email = $_POST['loginEmail'];
	$pass = $_POST['loginPass'];
	$errors = array();

	$sql  = "SELECT `hpass` FROM `users` WHERE `email` = ? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($hpass);

    if(!$stmt->fetch())
    	exitScript("Login failed");
    $stmt->close();

    if(!$hpass)
        exitScript("Login failed");
   

    $db_pass = $hpass;
    if(password_verify($pass, $db_pass))
    {
    	if($session->login($email))
    	   exitScript("Login successful");
        exitScript("Login failed");
    }
    else exitScript("Login failed");


    function exitScript($message)
	{
		global $errors;
        global $session;

        $session->addError($message);
        redirect_to($session->current_page);
	}
?>