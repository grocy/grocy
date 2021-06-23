function usersettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	$scope("#locale").val(Grocy.UserSettings.locale);

	RefreshLocaleNumberInput();

}
