<?php
	require_once "../../includes/initialize.php";

	$session->logout();
    exitScript("Loged out");

    function exitScript($message)
    {
        global $errors;
        global $session;

        $session->addError($message);
        redirect_to($session->current_page);
    }
?>