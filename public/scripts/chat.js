function Chat(socket)
{
	var socket = socket;
	var typingBox = [];
	var scrolling = false;
	var chatWindows = [];
	var contacts = [];
	var remoteVideoStatus = $("#remoteVideoStatus");
	var remoteAudioStatus = $("#remoteAudioStatus");
	var localVideoStatus = $("#localVideoStatus");
	var localAudioStatus = $("#localAudioStatus");
	var localUserCnt = $("#localUser");
	var localVideo = $("#localVideo");
	var remoteVideo = $("#remoteVideo");
	var isChannelReady;
	var isInitiator = false;
	var isStarted = false;
	var localStream = undefined;
	var pc = null;
	var remoteStream = undefined;
	var turnReady = false;
	var called_id = undefined;

	loadInitialContent();
	
	socket.on("socket_alive", function(data){
		var who = data.who.toString();
		console.log("Contact " + who + " alive");
		contacts[who].find(".status").removeClass("offline").addClass("online");
	});


	socket.on("socket_dead", function(data){
		var who = data.who.toString();
		contacts[who].find(".status").removeClass('online').addClass('offline');
	});


	socket.on("get_connections_status", function(data){
		for(var i=0; i<data.length; i++)
		{
			var e = data[i];
			var contact = contacts[e.id.toString()];
			if(e.status === true)
				contact.find(".status").removeClass('offline').addClass('online');
			else 
				contact.find(".status").removeClass('online').addClass('offline');
		}
	});


	socket.on("message_received", function(data){
		var id = data.from.toString();
		if(chatWindows[id] !== undefined)
		{
			var time = new Date();
			var msg = {id:'x', msg:data.message, time:time}
			var msgCnt = createRemoteMessage(msg);
			openChatWindow(id, msgCnt); 
			chatWindows[id].find(".messagesArea").append(msgCnt);
			
			/*$.getJSON("ajax/get_remote_message.php", {from:data.from}, function(msg){
				var msgCnt = createRemoteMessage(msg);
				chatWindows[id].find(".messagesArea").append(msgCnt);
				openChatWindow(id);
			}); */
		}
		else
		{
			$.getJSON("ajax/messages.php", {id:id, offset:0}, function(chat){
				createChatWindow(chat, id);
			});
		}
	});


	socket.on("user_typing", function(data){
		console.log("Typing " + data.from);
	    var from = data.from.toString();

	    if(chatWindows[from] !== undefined)
	    {
	    	if(typingBox[from] !== undefined)
	    	{
	    		typingBox[from].remove();
	    		typingBox[from] = undefined;
	    	}

	    	var typingArea = chatWindows[from].find(".typingArea").first();
	    	typingBox[from] = $("<div class='typingBox'></div>").appendTo(typingArea);
	    	adjustChatScroller(chatWindows[from].find(".messagesArea"));
	    }
	});


	socket.on("user_not_typing", function(data){
		console.log("Not typing" + data.from);
		var from = data.from.toString();
		if(typingBox[from] != undefined)
		{
			typingBox[from].remove();
			typingBox[from] = undefined;
		}
	});

	socket.on('request_call', function(request){
		console.log("Call requested from " + request.from);
		displayCallRequestWindow(request.from);
	});	

	socket.on('video_state_changed', function(data){
		if(data.state == true)
		{
			remoteVideo.removeClass("hidden");
			remoteVideoStatus.addClass("hidden");
		}
		else
		{
			remoteVideo.addClass("hidden");
			remoteVideoStatus.removeClass("hidden");
		}
	});

	socket.on('audio_state_changed', function(data){
		if(data.state == true)
			remoteAudioStatus.addClass("hidden");
		else
			remoteAudioStatus.removeClass("hidden");
	});

	socket.on('call_rejected', function(data){
		removeGracefully($(".callingContainer"));
		localUserCnt.addClass("hidden");
		resetVariables();
	});

	socket.on('accept_call', function(data){
		console.log("Call accepted from "+ data.from);
		initiateCallInterface();
		isInitiator = true;
		isChannelReady = true;
		called_id = data.from;
		maybeStart();
	});

	

	socket.on("hangup_requested", function(response){
		socket.emit("hangup_confirm", {to:called_id});
		
		console.log("Hangup request received");
		hangUp();
	});

	$("#connections").on('click', '.contact', function(){
		var id = $(this).attr("data-id").toString();
		var chatWindow = getChatWindow(id);
	});

	socket.on('media_message', function (message){	
	  console.log('Client received message: ', message);
	  console.log("TYPE: ", message.type);
	  if (message.type == "offer") {
	  	
	    if (!isInitiator && !isStarted) {
	      maybeStart();
	    }
	    pc.setRemoteDescription(new RTCSessionDescription(message));
	    doAnswer();
	  } else if (message.type == "answer" && isStarted) {
	  	
	    pc.setRemoteDescription(new RTCSessionDescription(message));
	  } else if (message.type == "candidate" && isStarted) {
	  	
	    var candidate = new RTCIceCandidate({
	      sdpMLineIndex: message.label,
	      candidate: message.candidate
	    });
	    pc.addIceCandidate(candidate);
	  }
	});

	$("#chatWindowsContainer").on('click', '.closeWindow', function(){
		var parent = $(this).parent().parent();
		parent.parent().addClass("free");
		var id = $(parent).attr("data-id");
		parent.remove();
		chatWindows[id] = undefined;
		var chats = $("#chatWindowsContainer .chatColumn:visible");
		var n = chats.length-1;
		
		for(var i=n; i>= 0; i--)
		{
			var chat = $(chats[i]);
			
			if(chat.is(".free"))
			{
				var j=i-1;
				var chat2 = $(chats[j]);
			
				while(j >= 0)
				{
					if(!chat2.is(".free"))
					{
						chat2.children().first().appendTo(chat);
						console.log("Append: ", chat2.children().first());
						chat.removeClass("free");
						chat2.addClass("free");
						break;
					}
					j--;
				}
			}
		}
	});

	$("#chatWindowsContainer").on('keyup', '.textbox', function(e){
		var to = $(this).attr("data-id");
		var length = $(this).val().length;

		 if(length>=1)
	        socket.emit("user_typing", {to:to});
	     else if (length == 0)
	      	socket.emit("user_not_typing", {to:to});

		if(e.which == 13)
		{
			var msg = $(this).val();
			var id = to.toString();
			$(this).val("");
			$.post("ajax/send_message.php", {msg:msg, to:to}, function(data){
				console.log(data);
			/*	 var msgCnt = createLocalMessage(data);	
				 var messagesArea = chatWindows[id].find(".messagesArea");
				 messagesArea.append(msgCnt);
				adjustChatScroller(messagesArea); */
			}, "json"); 
			
			var time = new Date();
			var data = {id:'x', time:time, msg:msg};
			var msgCnt = createLocalMessage(data);	
			var messagesArea = chatWindows[id].find(".messagesArea");
			messagesArea.append(msgCnt);
			adjustChatScroller(messagesArea);

			socket.emit("message", {msg:msg, to:to});
			socket.emit("user_not_typing", {to:to});
			
		}
	});
	
	$("#chatWindowsContainer").on('click', '.minimize', function(){
		$(this).parent().next().toggle();
	});

	$("#chatWindowsContainer").on("click", '.camera', function(){
		 var to = $(this).parent().parent().attr("data-id");
		 isInitiator = true;
		 called_id = to;
		 console.log("Calling ", called_id);
		 getUserMedia(constraints, handleUserMedia, handleUserMediaError);

	});

	$("body").on("click", '.acceptCall', function(){
		 var to = $(this).parent().parent().attr("data-from");
		 called_id = to;
		 isInitiator = false;
		 console.log("Accepting call to ", called_id);
		 getUserMedia(constraints, handleUserMedia, handleUserMediaError);
		 initiateCallInterface();
	});

	$("body").on('click', '.rejectCall', function(){
		var parent = $(this).parent().parent();
		var to = parent.attr("data-from");
		resetVariables();
		var data = {to:to};
		console.log(data);
		removeGracefully(parent);
		socket.emit('call_rejected', data);
	});

	$("#localUser").hover(function(){
		$(".controlsWrapper").stop(true, true).fadeTo(300, 1);
	}, function(){
		$(".controlsWrapper").stop(true, true).fadeTo(300, 0);
	});

	$("#hangup").click(function(){
		console.log("hangup: ", called_id);
		socket.emit("hangup_requested", {to:called_id});
		hangUp();
		console.log("hangup request sent");
	});

	$("#pauseAudio").click(function(){
		var new_state = !localStream.getAudioTracks()[0].enabled;
		socket.emit('audio_state_changed', {to:called_id, state:new_state});
		localStream.getAudioTracks()[0].enabled = new_state;
		if(!new_state)
			localAudioStatus.removeClass("hidden");
		else localAudioStatus.addClass("hidden");
	});

	function loadInitialContent(){
		if (location.hostname != "localhost" && location.hostname != "127.0.0.1") {
		  requestTurn('https://computeengineondemand.appspot.com/turn?username=41784574&key=4080218913');
		}

		$.getJSON("ajax/get_connections.php", function(data){
			createChat(data);
			var users = [];
			for(var i=0; i<data.length; i++)
				users.push(data[i].id);

			socket.emit("socket_alive", {connections:users});
		});

		makeDraggable($("#localUser"));
	}

	function createChat(data){
		var parent = $("#connections");
		for(var i=0; i<data.length; i++)
		{
			var e = data[i];
			var name = e.name + " " + e.last;
			var container = $("<li></li>").attr("data-id", e.id).appendTo(parent);
			container.addClass("list-group-item contact clearfix");
			var imgCnt = $("<div></div>").addClass("imgCnt").appendTo(container);
			var img = $("<img>").attr("src", "images/user.png").appendTo(imgCnt);
			var textCnt = $("<div></div>").addClass("textCnt").text(name).appendTo(container);
			var statusCnt = $("<span></span>").addClass("statusCnt").appendTo(container);
			var status = $("<div></div>").addClass("status").appendTo(statusCnt);
			contacts[e.id.toString()] = container;
		}
	}

	function getChatWindow(id)
	{
		if(chatWindows[id] === undefined)
		{
			$.getJSON("ajax/messages.php", {id:id, offset:0}, function(data){
				createChatWindow(data, id);
			});
		}
		else
		{
			if(!chatWindows[id].parent().is(":visible"))
			{
				chatWindows[id].parent().html("");
				$.getJSON("ajax/messages.php", {id:id, offset:0}, function(data){
					createChatWindow(data, id);
				});
			}
			else
			{
				chatWindows[id].find(".chatArea").show();
				var messagesArea = chatWindows[id].find(".messagesArea")[0];
				messagesArea.scrollTop = messagesArea.scrollHeight;	
				chatWindows[id].find(".textbox").focus();
			}
			
		}

		return chatWindows[id];
	}

	function createChatWindow(data, id)
	{
		var user = data.user;
		var messages = data.messages;
		var name = user.name + " " + user.last;
		//var chatWindowsContainer = $("#chatWindowsContainer"); 
		var col = $("#chatWindowsContainer .chatColumn.free:visible").last().removeClass("free");
	
		var div = $("<div class='chatWindow panel panel-primary'></div>").attr("data-id", id);
		var titleBar = $("<div class='titleBar panel-heading'></div>").text(name).appendTo(div);
		var close = $("<span class='glyphicon glyphicon-remove closeWindow tool'></span>").appendTo(titleBar);
		var minimize = $("<span class='glyphicon glyphicon-save minimize tool'></span>").appendTo(titleBar);
		var camera = $("<span class='glyphicon glyphicon-facetime-video camera tool'></span>").appendTo(titleBar);
		var chatArea = $("<div class='chatArea panel-body'></div>").appendTo(div);
		var messagesArea = $("<div class='messagesArea media-list'></div>").attr("data-page", "0").attr("data-id", id).appendTo(chatArea);
		var typingArea = $("<div class='typingArea'></div>").appendTo(chatArea);
		var chatBar = $("<div class='chatBar input-group'></div>").appendTo(chatArea);
		var textarea = $("<input type='text' class='textbox form-control'>").attr("data-id", id).appendTo(chatBar);
		//chatWindowsContainer.append(div);
		div.appendTo(col);
		
		var n = messages.length-1;
		for(var i=n; i>=0; i--)
		{
			var m = messages[i];
			createMessage(m, messagesArea, id, true);
		}

		chatWindows[id] = div;
		messagesArea[0].scrollTop = messagesArea[0].scrollHeight;
		chatWindows[id].find(".chatArea").show();
		chatWindows[id].find(".textbox").focus();

		$(".messagesArea").scroll(function(){
			if(scrolling)
				return;

			if($(this).scrollTop() < 20)
			{
				scrolling = true;
				var page = parseInt($(this).attr("data-page"));
				var offset = page +1;
				var id = $(this).attr("data-id");
				var ob = $(this);
				
				$.getJSON("ajax/messages.php", {id:id, offset:offset}, function(data){
					var messages = data.messages;
					
					if(messages.length > 0)
					{
						page += 1;
						ob.attr("data-page", page);
						ob[0].scrollTop = 400;
						var n = messages.length;
						for(var i=0; i<n; i++)
						{
							var m = messages[i];
							createMessage(m, messagesArea, id, false);
						}
					}
					scrolling = false;
				});
			}
		
		});
	}

	function createMessage(m, parent, id, append)
	{
		var msgCnt = $("<li class='media clearBoth'></li>").attr("data-id", m.id);
		if(append)
			msgCnt.appendTo(parent);
		else msgCnt.prependTo(parent);

		if(m.sender == id)
		{
			var a = $("<a class='media-left' href='#'></a>").appendTo(msgCnt);
			var img = $("<img class='chatImgCnt'>").attr("src", "images/user.png").appendTo(a);
			msgCnt.addClass("remoteMsg");
		}
		else
		{
			msgCnt.addClass("pull-right localMsg");	
		}
		var textCnt = $("<div></div>").addClass("media-body msgText").text(m.message).appendTo(msgCnt);
	}

	function adjustChatScroller(messagesArea)
	{
		messagesArea[0].scrollTop = messagesArea[0].scrollHeight;
	}

	function makeDraggable(e)
	{
		e.draggable({ containment: "parent", scroll: false });	
	}

	function openChatWindow(id, msgCnt)
	{
		var chatArea = chatWindows[id].find(".chatArea");
		if(!chatArea.is(":visible"))
		{
			chatArea.show();
			chatWindows[id].find(".textbox").focus();
		}

		var messagesArea = chatWindows[id].find(".messagesArea");
		messagesArea.append(msgCnt);
		adjustChatScroller(messagesArea);
	}

	function createRemoteMessage(data)
	{	
		var msgCnt = $("<li class='media remoteMsg clearBoth'></li>").attr("data-id", data.id);
		var a = $("<a class='media-left' href='#'></a>").appendTo(msgCnt);
		var img = $("<img class='chatImgCnt'>").attr("src", "images/user.png").appendTo(a);
		var textCnt = $("<div></div>").addClass("media-body msgText").text(data.msg).appendTo(msgCnt)
		return msgCnt;
	}

	function createLocalMessage(data)
	{
		var msgCnt = $("<li class='media localMsg clearBoth'></li>").attr("data-id", data.id);
		var textCnt = $("<div></div>").addClass("msgText").text(data.msg).appendTo(msgCnt)
		return msgCnt;
	}

	function removeGracefully(elem, time, callback)
	{
		time = typeof time != 'undefined' ? time : 300;
		$(elem).fadeTo(300, 0,  function(){
			$(this).remove();
			if(typeof callback != 'undefined')
				callback();
		});
	}

	function hangUp(){
		console.log('Hanging up.');
		resetVariables();
		localUserCnt.addClass("hidden");
	}

	function initiateCallInterface(){
		console.log("Inititating call interface...");
		localVideo.removeClass("fullsize").addClass("smallsize");
		remoteVideo.addClass("fullsize");
		localUserCnt.removeClass("hidden");
		$(".callingContainer").remove();
		$(".callRequestContainer").remove();
	}

	function handleUserMedia(stream) {
	  console.log('Adding local stream.');
	  localVideo.attr("src", (window.URL && window.URL.createObjectURL(stream)) || stream);
	  localStream = stream;
	  localUserCnt.removeClass("hidden");
	//alert("OK "+called_id);
	  if (isInitiator) 
	  {
	  	 socket.emit('request_call', {to:called_id});
		 displayCallingWindow(called_id);
	  }
	  else
	  {
	  	socket.emit('accept_call', {to:called_id});
	  	isChannelReady = true;
	  }
	}

	function handleUserMediaError(error){
	  console.log('getUserMedia error: ', error);
	  getUserMedia(constraintsAlt, handleUserMedia, handleUserMediaError);
	}

	function resetVariables()
	{
		if(localStream != null)
			localStream.stop();
		if(remoteStream != null)
			remoteStream.stop();
		called_id = undefined;
		isChannelReady = false;
		isStarted = false;
		isInitiator = false;
		localStream = null;
		remoteStream = null;
		localVideo.attr("src", null);
		remoteVideo.attr("src", null);
		if(pc)
			pc.close();
		pc=null;
	}

	function sendMessage(message){
		console.log('Client sending message: ', message);
	    socket.emit('media_message', message);
	}
	
/////////// WebRTC initializations //////////////
var pc_config = {'iceServers': [{'url': 'stun:stun.l.google.com:19302'}, 
{url:'stun:stun1.l.google.com:19302'}, {url:'stun:stun2.l.google.com:19302'}, {url:'stun:stun3.l.google.com:19302'},
{url:'stun:stun4.l.google.com:19302'}, {url:'stun:stunserver.org'}]};

var pc_constraints = {'optional': [{'DtlsSrtpKeyAgreement': true}]};

// Set up audio and video regardless of what devices are present.
var sdpConstraints = {'mandatory': {
  'OfferToReceiveAudio':true,
  'OfferToReceiveVideo':true }};

 var constraints = {video: {
        mandatory : {
            minWidth    : 640,
            minHeight   : 360, 
            maxWidth   : 1280,
            maxHeight	: 720     
        },
        optional: [
	    { width: { min: 320 }},
	    { width: { max: 800 }},
	    { height: { min: 240 }},
	    { height: { max: 720 }},
	    { facingMode: "user" }]
    }, audio:true}; 

var qvgaConstraints = {
	video:{
		mandatory:{
			maxWidth: 320,
			maxHeight: 180
		}
	}
};

var vgaConstraints = {
	video:{
		mandatory:{
			maxWidth: 640,
			maxHeight: 360
		}
	},
	audio:true
};

var hdConstraints = {
	video:{
		mandatory:{
			maxWidth: 1280,
			maxHeight: 720
		}
	}
};

var constraintsAlt = {
	video: false,
	audio: true
};

///////////////// Web RTC functions //////////////////////////


function maybeStart() {
	console.log("isStarted: " + isStarted);
	console.log("localStream: " + (localStream != undefined) );
	console.log("isChannelReady: " + isChannelReady);
	console.log('isInitiator', isInitiator);
  if (!isStarted && typeof localStream !== undefined && isChannelReady) {
    createPeerConnection();
    pc.addStream(localStream);
    isStarted = true;
   
    if (isInitiator) {
      doCall();
    }
  }
}

window.onbeforeunload = function(e){
	if(called_id)
	{
		console.log("hangup: ", called_id);
		socket.emit("hangup_requested", {to:called_id});
		hangUp();
		console.log("hangup request sent");
	}
}

/////////////////////////////////////////////////////////

function createPeerConnection() {
  try {
    pc = new RTCPeerConnection(pc_config);
    pc.onicecandidate = handleIceCandidate;
    pc.onaddstream = handleRemoteStreamAdded;
    pc.onremovestream = handleRemoteStreamRemoved;
    console.log('Created RTCPeerConnnection');
  } catch (e) {
    console.log('Failed to create PeerConnection, exception: ' + e.message);
    alert('Cannot create RTCPeerConnection object.');
      return;
  }
}

function handleIceCandidate(event) {
  //console.log('handleIceCandidate event: ', event);
  if (event.candidate) {
  	var message = 
  		{
  			to:called_id,
  			type: 'candidate', 
	  		message :{
	  		  type: 'candidate',
		      label: event.candidate.sdpMLineIndex,
		      id: event.candidate.sdpMid,
		      candidate: event.candidate.candidate
	  	}};
	console.log("331: ", message);
    sendMessage(message);
  } else {
    console.log('End of candidates.');
  }
}

function handleCreateOfferError(event){
  console.log('createOffer() error: ', e);
}

function doCall() {
  console.log('Sending offer to peer');
  pc.createOffer(setLocalAndSendMessage, handleCreateOfferError);
}

function doAnswer() {
  console.log('Sending answer to peer.');
  pc.createAnswer(setLocalAndSendMessage, null, sdpConstraints);
}

function setLocalAndSendMessage(sessionDescription) {
	console.log("sessionDescription: ", sessionDescription);
  // Set Opus as the preferred codec in SDP if Opus is present.
  sessionDescription.sdp = preferOpus(sessionDescription.sdp);
  pc.setLocalDescription(sessionDescription);
  var type = sessionDescription.type !== undefined ? sessionDescription.type : 'offer';
  var message = {message:sessionDescription, to:called_id, type:type};

  console.log("359: ", message);
  sendMessage(message);
}

function requestTurn(turn_url) {
  var turnExists = false;
  for (var i in pc_config.iceServers) {
    if (pc_config.iceServers[i].url.substr(0, 5) === 'turn:') {
      turnExists = true;
      turnReady = true;
      break;
    }
  }
  if (!turnExists) {
    console.log('Getting TURN server from ', turn_url);
    // No TURN server. Get one from computeengineondemand.appspot.com:
    $.getJSON('ajax/get_turn_server.php', {url:turn_url}, function(data){
    	var turnServer = JSON.parse(data);
    	for(var i=0; i<turnServer.uris.length; i++)
    	{
    			pc_config.iceServers.push({
		          'url': turnServer.uris[i],
		          'credential': turnServer.password,
		          'username': turnServer.username
		        });
		        
    	}
    console.log(turnServer);
        console.log(pc_config.iceServers);
        turnReady = true;
        console.log('Got TURN server: ', turnServer);
    });
  /*  var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
      if (xhr.readyState === 4 && xhr.status === 200) {
        var turnServer = JSON.parse(xhr.responseText);
      	console.log('Got TURN server: ', turnServer);
        pc_config.iceServers.push({
          'url': 'turn:' + turnServer.username + '@' + turnServer.turn,
          'credential': turnServer.password
        });
        turnReady = true; 
      }
    };
    xhr.open('GET', turn_url, true);
    xhr.send(); */
  }
}

function handleRemoteStreamAdded(event) {
  console.log('Remote stream added.');
  remoteVideo.attr("src", window.URL.createObjectURL(event.stream));
  remoteStream = event.stream;
}

function handleRemoteStreamRemoved(event) {
  console.log('Remote stream removed. Event: ', event);
}



///////////////////////////////////////////

// Set Opus as the default audio codec if it's present.
function preferOpus(sdp) {
  var sdpLines = sdp.split('\r\n');
  var mLineIndex;
  // Search for m line.
  for (var i = 0; i < sdpLines.length; i++) {
      if (sdpLines[i].search('m=audio') !== -1) {
        mLineIndex = i;
        break;
      }
  }
  if (mLineIndex === null) {
    return sdp;
  }

  // If Opus is available, set it as the default in m line.
  for (i = 0; i < sdpLines.length; i++) {
    if (sdpLines[i].search('opus/48000') !== -1) {
      var opusPayload = extractSdp(sdpLines[i], /:(\d+) opus\/48000/i);
      if (opusPayload) {
        sdpLines[mLineIndex] = setDefaultCodec(sdpLines[mLineIndex], opusPayload);
      }
      break;
    }
  }

  // Remove CN in m line and sdp.
  sdpLines = removeCN(sdpLines, mLineIndex);

  sdp = sdpLines.join('\r\n');
  return sdp;
}

function extractSdp(sdpLine, pattern) {
  var result = sdpLine.match(pattern);
  return result && result.length === 2 ? result[1] : null;
}

// Set the selected codec to the first in m line.
function setDefaultCodec(mLine, payload) {
  var elements = mLine.split(' ');
  var newLine = [];
  var index = 0;
  for (var i = 0; i < elements.length; i++) {
    if (index === 3) { // Format of media starts from the fourth.
      newLine[index++] = payload; // Put target payload to the first.
    }
    if (elements[i] !== payload) {
      newLine[index++] = elements[i];
    }
  }
  return newLine.join(' ');
}

// Strip CN from sdp before CN constraints is ready.
function removeCN(sdpLines, mLineIndex) {
  var mLineElements = sdpLines[mLineIndex].split(' ');
  // Scan from end for the convenience of removing an item.
  for (var i = sdpLines.length-1; i >= 0; i--) {
    var payload = extractSdp(sdpLines[i], /a=rtpmap:(\d+) CN\/\d+/i);
    if (payload) {
      var cnPos = mLineElements.indexOf(payload);
      if (cnPos !== -1) {
        // Remove CN payload from m line.
        mLineElements.splice(cnPos, 1);
      }
      // Remove CN line in sdp
      sdpLines.splice(i, 1);
    }
  }

  sdpLines[mLineIndex] = mLineElements.join(' ');
  return sdpLines;
}

/////////////////////////// Functions ///////////////////////////
	
	function displayCallingWindow(user)
	{
		$.getJSON('ajax/get_user.php', {user:user}, function(data){
			var text = data.name + " " + data.last;
			var div = $("<div class='callingContainer panel panel-info'></div>").attr("data-from", user);
			var heading = $("<div class='panel-heading text-center'></div>").appendTo(div);
			var calling = $("<h3 class='panel-title'></h3>").html("Calling").appendTo(heading);
			var body = $("<div class='panel-body'></div>").appendTo(div);
			var title = $("<h2 class=''></h2>").html(text).appendTo(body);
			$("#left").append(div);
			makeDraggable(div);
			console.log("Calling window displayed ");
		});
	}

	function displayCallRequestWindow(user)
	{
		$.getJSON('ajax/get_user.php', {user:user}, function(data){
			var div = $("<div class='panel panel-info callRequestContainer'></div>").attr("data-from", user);
			var heading = $("<div class='panel-heading'></div>").appendTo(div);
			var title = $("<h2 class='panel-title'></h2>").html(data.name + " " + data.last + " is calling you").appendTo(heading);
			var cnt = $("<div class='panel-body'></div>").appendTo(div);
			var accept = $("<div class='glyphicon glyphicon-ok-circle acceptCall callBtn pull-left'></div>").appendTo(cnt);
			var rejeect = $("<div class='glyphicon glyphicon-remove-circle rejectCall callBtn pull-right'></div>").appendTo(cnt);
			$("#left").append(div);
			makeDraggable(div);
			console.log("Call request window displayed ", div);
		});
	}
}