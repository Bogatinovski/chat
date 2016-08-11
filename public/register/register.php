<?php 
	require_once "../../includes/initialize.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<link rel="stylesheet" type="text/css" href="css/register.css">
	</head>
	<body>
		<form id="registerForm" class='form' method="POST">
			<h4>Register your account</h4>
			<ul>
				<li>
					<span class='lbl'>Email: </span>
					<input value="bogatinovski.dejan@gmail.com" class='input' id="email" type='text' name='email' placeholder="Enter email address">
					<span class="error" id="emailError"></span>
				</li>
				<li>
					<span class='lbl'>First name: </span>
					<input value="Dejan" class='input' id="first" type='text' name='first' placeholder="Enter fisrt name">
					<span class="error" id="firstError"></span>
				</li>
				<li>
					<span class='lbl'>Last name: </span>
					<input value="Bogatinovski" class='input' id="last" type='text' name='last' placeholder="Enter last name">
					<span class="error" id="lastError"></span>
				</li>
				<li>
					<span class='lbl'>Password: </span>
					<input class='input' id="password" type='password' name='pass' value='password' placeholder="Enter password">
					<span class="error" id="passError"></span>
				</li>
				<li>
					<span class='lbl'>Confirm password: </span>
					<input class='input' id="confPassword" type='password' name='confPass' value='password' placeholder="Confirm password">
					<span class="error" id="confError"></span>
				</li>
			</ul>

			<input type="submit" value="Register" id="register">
		</form>

		<form id="loginForm" class='form' method="POST">
			<h4>Log into your account</h4>
			<ul>
				<li>
					<div>Email: </div>
					<input type='text' id="loginEmail" name="loginEmail">
				</li>
				<li>
					<div>Password: </div>
					<input type='password' id="loginPass" name="loginPass">
				</li>
			</ul>

			<input type="submit" value="Log in" id="login">
			<div id="logout">
			Log Out
			</div>
		</form>


		
		<div id="message"></div>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/helper_func.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
	</body>
</html>