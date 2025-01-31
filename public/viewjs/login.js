setTimeout(function()
{
	$('#username').focus();
}, Grocy.FormFocusDelay);

if (GetUriParam('invalid') === 'true')
{
	$('#login-error').text(__t('Invalid credentials, please try again'));
	$('#login-error').removeClass('d-none');
}
