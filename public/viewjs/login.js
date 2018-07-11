$('#username').focus();

if (GetUriParam('invalid') === 'true')
{
	$('#login-error').text(L('Invalid credentials, please try again'));
	$('#login-error').removeClass('d-none');
}
