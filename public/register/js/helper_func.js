function message(message)
{
	if($("#message"))
		$("#message").html(message);
	else console.log(message);
}
function globalAjaxErrors(data, statusText, jqXHR)
{	
	message(data.responseText);
}