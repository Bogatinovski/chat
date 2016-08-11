<?php
	$session->setCurrentPage($_SERVER['REQUEST_URI']);
	if(!$session->is_logged_in())
	{
?>
<div id="user_bar" class='clearfix'>
	<div id="toggleRegisterForm" >Open registration form</div>
	<form id="registerForm" class='form' method="POST">
			<ul>
				<li>
					<input class='input' id="email" type='text' name='email' placeholder="Enter email address">
					<span class="error" id="emailError"></span>
				</li>
				<li>
					<input class='input' id="first" type='text' name='first' placeholder="Enter fisrt name">
					<span class="error" id="firstError"></span>
				</li>
				<li>
					<input class='input' id="last" type='text' name='last' placeholder="Enter last name">
					<span class="error" id="lastError"></span>
				</li>
				<li>
					<input class='input' id="password" type='password' name='pass' placeholder="Enter password">
					<span class="error" id="passError"></span>
				</li>
				<li>
					<input class='input' id="confPassword" type='password' name='confPass'  placeholder="Confirm password">
					<span class="error" id="confError"></span>
				</li>
				<li>
					<input type="submit" class='button' value="Register" id="register">
				</li>
			</ul>	
		</form>
	<form id="loginForm" class='form' method="POST" action="register/scripts/login.php">
			<table>
				<!--<tr>
					<td>Email: </td> <td>Password: </td> <td></td>
				</tr> -->
				<tr>
					<td><input type='text' id="loginEmail" name="loginEmail" placeholder="Email address"></td>
					<td><input type='password' id="loginPass" name="loginPass"  placeholder="Password"></td>
					<td><input type="submit" value="Log in" id="login" class="button"></td>
				</tr>
			</table>
			<!--<div id="message_bar"><?php echo $session->getErrors(); ?></div>-->
		</form>
	</div>
</div>

<?php
	}
	else
	{
?>

	<div id="user_bar" class='clearfix'>
	<div id="toggleRegisterForm" >Sign up</div>
	<div id="toggleAddConnectionForm" >Add connection</div> 
	<form id="registerForm" class='form' method="POST">
			<ul>
				<li>
					<input class='input' id="email" type='text' name='email' placeholder="Enter email address">
					<span class="error" id="emailError"></span>
				</li>
				<li>
					<input class='input' id="first" type='text' name='first' placeholder="Enter fisrt name">
					<span class="error" id="firstError"></span>
				</li>
				<li>
					<input class='input' id="last" type='text' name='last' placeholder="Enter last name">
					<span class="error" id="lastError"></span>
				</li>
				<li>
					<input class='input' id="password" type='password' name='pass' placeholder="Enter password">
					<span class="error" id="passError"></span>
				</li>
				<li>
					<input class='input' id="confPassword" type='password' name='confPass'  placeholder="Confirm password">
					<span class="error" id="confError"></span>
				</li>
				<li>
					<input type="submit" class='button' value="Register" id="register">
				</li>
			</ul>	
		</form>

	<form id="addConnectionForm">
		<input id="searchEmail" type="text" name="email" placeholder="Add email address to your connection">
		<input type="submit" value="Search" class="button">
	</form>

	<form id="logoutForm" method="POST" action="register/scripts/logout.php">
		<div id="user_status">
				<div class='barImg'>
					<img src="images/logo.png">
				</div>
			<div id="logoutInfo">
				 <?php echo $session->first; ?> </br> <?php echo $session->last; ?>
				<br><span id="logout">(Log out)</span>
			</div>
		</div>
		<!--<div id="message_bar"><?php echo $session->getErrors(); ?></div>-->
	</form>
	</div>
<?php
}
?>