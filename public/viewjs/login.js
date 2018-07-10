$('.logout-button').hide();
$('.logout-button-divider').hide();

$('#username').focus();

if (GetUriParam('invalid') === 'true')
{
	$('#login-error').text(L('Invalid credentials, please try again'));
	$('#login-error').show();
}
