setTimeout(function ()
{
	$('#username').focus();
}, Grocy.FormFocusDelay);

if (GetUriParam('invalid') === 'true')
{
	$('#login-error').text(__t('Invalid credentials, please try again'));
	$('#login-error').removeClass('d-none');
}

$("#login-button").on("click", function (e)
{
	e.preventDefault();

	$("#password_base64").val(btoa($("#password_input").val()));
	$("#login-form").trigger("submit");
});
