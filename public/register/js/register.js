$(document).ready(function(){

	$("#toggleRegisterForm").click(function(){
		$("#registerForm").slideToggle(400);
	});
	$("#registerForm").submit(function(e){
		e.preventDefault();
		if(!validate())
			return false;

		var jqXHR = $.post("scripts/process.php", $(this).serialize(), function(data){
			message(data.errors);
		}, "json");
		jqXHR.error(globalAjaxErrors);

	});

	$("#logout").click(function(){
		$("#logoutForm").submit();
	});

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
		}, 2000);
	}
});