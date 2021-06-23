function loginView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	$scope('#username').focus();

	if (GetUriParam('invalid') === 'true')
	{
		$scope('#login-error').text(__t('Invalid credentials, please try again'));
		$scope('#login-error').removeClass('d-none');
	}

}
