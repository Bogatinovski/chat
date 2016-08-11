$(document).ready(function(){

///////////// Node.js related javascript //////////////////
var local_ip = "localhost";
var public_ip = "77.28.107.38";
var used_ip = local_ip;
var socket_port = 3000;

var socket_string = "http://"+used_ip+":"+socket_port+"/chat";
var socket = io(socket_string);
var chat = new Chat(socket);
	
});