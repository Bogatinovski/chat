<?php  require_once "../includes/initialize.php"; ?>
<!DOCTYPE html>
<html>
	<head>
		<!--<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/user_bar.css">-->
		<!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
		<meta name='viewport' content='width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1'> 
		<meta charset="UTF"> 
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet"> 
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
		<!--<link rel="stylesheet" type="text/css" href="css/bootstrap/css/bootstrap.min.css">-->
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<!--<link rel="stylesheet" type="text/css" href="scripts/lib/custom-scrollbar/jquery.mCustomScrollbar.css">-->
	</head>
	<body>
		<div class="container-fluid" id="mainContainer">
			<?php require_once "register/profile_bar_bootsrap.php" ?>
			<div id="middle" class="row-fluid">
				<div id="left" class="col-sm-8 col-md-9">
					<div class="panel panel-default" id="leftSubCnt">
						<div id="videoChatContainer" class="container-fluid">
						<div id="localUser" class="container-fluid hidden">
							<div class="wrapper">
								<video id="localVideo" autoplay muted></video>
								<div class='controlsWrapper'>
									<div id="controls">
										<div id="pauseVideo" class="control">Toggle Video</div>
										<div id="pauseAudio" class="control">Toggle Audio</div> 
										<div id="hangup" class="control">Hang up</div>
									</div>
									<div id="status">
										<div id="remoteVideoStatus" class="hidden mediaStatus">Remote video is turned off</div>
										<div id="remoteAudioStatus" class="hidden mediaStatus">Remote audio is turned off</div>
										<div id="localVideoStatus" class="hidden mediaStatus">Local video is turned off</div>
										<div id="localAudioStatus" class="hidden mediaStatus">Local audio is turned off</div>
									</div>
								</div>
							</div>
						</div>
						<div id="remoteUser" >
							<video id="remoteVideo" autoplay></video>
						</div>
					</div>
					<div id="chatWindowsContainer" class="row">
						<div class="chatColumn free col-xs-12 col-sm-6 col-md-4 col-lg-3"></div>
						<div class="chatColumn free col-xs-12 col-sm-6 col-md-4 col-lg-3 hidden-xs"></div>
						<div class="chatColumn free col-xs-12 col-sm-6 col-md-4 col-lg-3 visible-md visible-lg"></div>
						<div class="chatColumn free col-xs-12 col-sm-6 col-md-4 col-lg-3 visible-lg"></div>
					</div>
					</div>
				</div>

				<div id="right" class="col-sm-4 col-md-3">
					<div class="panel panel-primary">
						<div class="panel-heading text-center">Your connections</div>
						<ul id="connections" class="list-group">

						</ul>
					</div>
				</div>
			</div>
			<div class="row-fluid" id="bottom">
				<div id="message"></div>
			</div>
		</div>

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js" type="text/javascript"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		<script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
		<script src="scripts/functions.js" type="text/javascript"></script>
		<!--<script src="scripts/lib/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js" type="text/javascript"></script>-->
		<script src="scripts/register.js" type="text/javascript"></script>
		<script src="scripts/chat.js" type="text/javascript"></script>
		<script src="scripts/script.js" type="text/javascript"></script>
		<script src="scripts/lib/adapter.js" type="text/javascript"></script>
		<script src="scripts/webrtc.js" type="text/javascript"></script>
	</body>
</html>