<?php
	$session->setCurrentPage($_SERVER['REQUEST_URI']);

	if(!$session->is_logged_in())
	{
?>
<nav id="myNavbar" class="navbar navbar-default navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Chat</a>
        </div>
        <center>
            <div class="navbar-collapse collapse" id="navbar-main">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sign up <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li class="divider"></li>
                            <li><a href="#">Separated link</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="#">One more separated link</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <form class="navbar-form navbar-right" method="POST" action="register/scripts/login.php">
                    <div class="form-group">
                        <input type="text" class="form-control" name="loginEmail" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="loginPass" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-default">Sign In</button>
                </form>
            </div>
        </center>
    </div>
</nav>

<?php
	}
	else
	{
?>
	<nav id="myNavbar" class="navbar navbar-default navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Chat</a>
        </div>
        <center>
            <div class="navbar-collapse collapse" id="navbar-main">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Sign up <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li class="divider"></li>
                            <li><a href="#">Separated link</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="#">One more separated link</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <form id="logoutForm" class="navbar-form navbar-right clearfix" method="POST" action="register/scripts/logout.php">
                   	<div id="user_status" class="pull-right">
						<div class='barImg'>
							<img src="images/logo.png">
						</div>
						<div id="logoutInfo" class="pull-right">
							 <?php echo $session->first; ?> </br> <?php echo $session->last; ?>
							<br><span id="logout">(Log out)</span>
						</div>
					</div>
                </form>
            </div>
        </center>
    </div>
</nav>
<?php
}
?>
