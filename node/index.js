var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

var redis = require("redis");
var co = require("./cookie.js");

var public_ip = "77.28.107.38";
var local_ip = "127.0.0.1";
var redis_port = 6379;
var socket_port = 3000;
var used_ip = public_ip;

app.get('/', function(req, res){

});

var chat=io.of("/chat");

chat.on('connection', function(socket){

    var cookieManager = new co.cookie(socket.handshake.headers.cookie);
    var client = new redis.createClient();

    executeInSession(function(session){
        socket.join(session.unique_id);
        socket.id = session.unique_id;
        console.log("User " + session.unique_id + " joined");
    });

    socket.on('disconnect', function(){
        console.log("Socket " + socket.id + " disconnected");

      for(var i=0; i<socket.rooms.length; i++)
            socket.leave(socket.rooms[i]);
        
        chat.adapter.rooms[socket.id] = undefined;

        if(socket.connections !== undefined)
        {
            for(var i=0; i<socket.connections.length; i++)
                chat.to(socket.connections[i]).emit("socket_dead", {who:socket.id});
           
            socket.connections == undefined;
        }

        socket.id = undefined;
    });

    socket.on('media_message', function (message) {
        executeInSession(function(session){
            chat.to(message.to).emit('media_message', message.message);
            console.log("E:media_message ("+session.unique_id+"): ", message);
        });
    });

    socket.on('video_state_changed', function(request){
        executeInSession(function(session){
            chat.to(request.to).emit('video_state_changed', {state:request.state});
            console.log("E:video_state_changed ("+session.unique_id+"): ", request);
        });
    });

    socket.on('audio_state_changed', function(request){
        executeInSession(function(session){
            chat.to(request.to).emit('audio_state_changed', {state:request.state});
            console.log("E:audio_state_changed ("+session.unique_id+"): ", request);
        });
    });

    socket.on('call_rejected', function(request){
        executeInSession(function(session){
            chat.to(request.to).emit('call_rejected');
            console.log("E:call_rejected ("+session.unique_id+"): ", request);
        });
    });

    socket.on('hangup_requested', function(request){
        executeInSession(function(session){
            leaveVideoChat(socket, session, request)
            chat.to(request.to).emit('hangup_requested');
            console.log("E:hangup_requested ("+session.unique_id+"): ", request);
        });
    });

    socket.on('hangup_confirm', function(request){
        executeInSession(function(session){
            leaveVideoChat(socket, session, request);
            console.log("E:hangup_confirm ("+session.unique_id+"): ", request);
        });
    });

    socket.on('request_call', function(request){
        executeInSession(function(session){
             var room = session.unique_id + ";" + request.to;
             socket.join(room);
             chat.to(request.to).emit('request_call', {from:session.unique_id});
             console.log("E:request_call ("+session.unique_id+"): ", request);
        });
    });

    socket.on('accept_call', function(request){
        executeInSession(function(session){
            var room = request.to + ";" + session.unique_id;
            socket.join(room);
            chat.to(request.to).emit('accept_call', {from:session.unique_id});
            console.log("E:accept_call ("+session.unique_id+"): ", request);
        });
    });

    socket.on("message", function(message){
	    executeInSession(function(session){
            var ob = {from: session.unique_id, message:message.msg};
            chat.to(message.to).emit("message_received", ob);
            console.log("E:message ("+session.unique_id+"): ", message);
        });
    });

     socket.on("user_typing", function(data){
        executeInSession(function(session){
            var ob = {from: session.unique_id};
            chat.to(data.to).emit("user_typing", ob);
        });
    });

    socket.on("user_not_typing", function(data){
        executeInSession(function(session){
            var ob = {from: session.unique_id};
            chat.to(data.to).emit("user_not_typing", ob);
        });
    });

	socket.on('socket_alive', function(data){
        executeInSession(function(session){
            var connections = data.connections;
            socket.connections = connections;
            
            var result = [];
            
            for(var i=0; i<connections.length; i++)
            {
                 var id = connections[i].toString();
                if(chat.adapter.rooms[id] !== undefined)
                    result.push({id:id, status: true});
                else
                    result.push({id:id, status: false});

                 chat.to(connections[i]).emit("socket_alive", {who:session.unique_id});
                 chat.to(session.unique_id).emit("get_connections_status", result);
            }
 
        });	   
	});

    function leaveVideoChat(socket, session, request)
    {
        var room1 = session.unique_id + ";" + request.to;
        var room2 = request.to + ";" + session.unique_id; 
        for(var i=0; i<socket.rooms.length; i++)
            if(socket.rooms[i] == room1 || socket.rooms[i] == room2)
                 socket.leave(socket.rooms[i]);
            
        chat.adapter.rooms[room1] = undefined;
        chat.adapter.rooms[room2] = undefined;
    }

    function executeInSession(callback)
    {
        console.log("Session");
        client.get("sessions/"+cookieManager.get("PHPSESSID"), function(error, result){
           if(error){
            console.log("error : " + error);
            }
            else if(result && result.toString() != ""){
                callback(JSON.parse(result));
            }else{
                console.log("Session does not exist");
            }
        });
    }

    function log(room, messages){
        var array = [">>> Message from server: "];
      for (var i = 0; i < messages.length; i++) {
        array.push(messages[i]);
      }
        chat.to(room).emit('log', array);
    }
});

http.listen(socket_port, function(){
  console.log('listening on *:' + socket_port);
});