function loginView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (scope) => $(scope).find(scope);
	}

	$scope('#username').focus();

	if (Grocy.GetUriParam('invalid') === 'true')
	{
		$scope('#login-error').text(__t('Invalid credentials, please try again'));
		$scope('#login-error').removeClass('d-none');
	}

}


window.loginView = loginView
