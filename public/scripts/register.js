$(document).ready(function(){

	$("#addConnectionForm").submit(function(e){
		e.preventDefault();
		var email = $("#searchEmail").val();
		if(!validateEmail(email))
		{
			message_bar("Invalid email");
			return false;
		}

		var jqXHR = $.post("ajax/add_connection.php", $(this).serialize(), function(data){
			message_bar(data.message);
		}, "json");
	});

	$("#toggleRegisterForm").click(function(){
		$("#addConnectionForm").slideUp(200, function(){
			$("#registerForm").slideToggle(400);
		});
		
	});

	$("#toggleAddConnectionForm").click(function(){
		$("#registerForm").slideUp(200, function(){
			$("#addConnectionForm").slideToggle(400);
		});
		
	});

	$("#registerForm").submit(function(e){
		e.preventDefault();
		if(!validate())
			return false;

		var jqXHR = $.post("ajax/process.php", $(this).serialize(), function(data){
			if(data.status=="success")
				$("#registerForm").slideUp(400);
			message_bar(data.errors);
		}, "json");
		jqXHR.error(globalAjaxErrors);

	});

	$("#logout").click(function(){
		$("#logoutForm").submit();
	});

	function validateEmail(email)
	{
		if(email.length < 3)
			return false;

		var atpos = email.indexOf("@");
    	var dotpos = email.lastIndexOf(".");
	    if (atpos< 1 || dotpos<atpos+2 || dotpos+2>=email.length) 
	    	return false;
	    return true;
	}

	function validate()
	{
		var email = $("#email").val();
		var first = $("#first").val();
		var last = $("#last").val();
		var pass = $("#password").val();
		var conf = $("#confPassword").val();

		 $("#registerForm .error").text("");

		var valid = true;
		if(email.length < 3)
		{
			valid = false;
			$("#emailError").text("Enter valid email address");
		}
		var atpos = email.indexOf("@");
    	var dotpos = email.lastIndexOf(".");
	    if (atpos< 1 || dotpos<atpos+2 || dotpos+2>=email.length) {
	        $("#emailError").text("Not a valid e-mail address");
	        valid = false;
	    }

	    if(first.length == 0)
		{
			valid = false;
			$("#firstError").text("Enter first name");
		}
		if(last.length == 0)
		{
			valid = false;
			$("#lastError").text("Enter last name");
		}

		if(pass.length < 8)
		{
			valid = false;
			$("#passError").text("Minimum password length 7");
		}
		if(conf != pass)
		{
			valid = false;
			$("#confError").text("Passwords does not match");
		}
		return valid;
	}

	function generateErrors(errors)
	{
		var ul = "<ul>";
		for(var i=0; i<errors.length; i++)
			ul += "<li>"+errors[i]+"</li>";
		ul += "</ul>";
		return ul;
	}

	function message_bar(message){
		$("#message").html(message);
		setTimeout(function() {
			$("#message").html("");
		}, 3000);
	}
});