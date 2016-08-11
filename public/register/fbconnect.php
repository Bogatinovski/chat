<?php
	require_once 'facebook-php-sdk-v4-4.0-dev/autoload.php'; //include the facebook php sdk

	use Facebook\FacebookSession;
	use Facebook\FacebookRequest;
	use Facebook\GraphUser;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookRedirectLoginHelper;
	
	FacebookSession::setDefaultApplication('537925099646934','86400bb2cb2d20349fc30914d9c796f7');
	
	$session = new FacebookSession('access-token-here');
	try {
	  $me = (new FacebookRequest(
	    $session, 'GET', '/me'
	  ))->execute()->getGraphObject(GraphUser::className());
	  echo $me->getName();
	} catch (FacebookRequestException $e) {
	  // The Graph API returned an error
	} catch (\Exception $e) {
	  // Some other error occurred
	}

?>