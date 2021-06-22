function loginView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	$('#username').focus();
	
	if (GetUriParam('invalid') === 'true')
	{
		$('#login-error').text(__t('Invalid credentials, please try again'));
		$('#login-error').removeClass('d-none');
	}
	
}
