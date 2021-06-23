function usersettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (scope) => $(scope).find(scope);
	}

	$scope("#locale").val(Grocy.UserSettings.locale);

	RefreshLocaleNumberInput();

}


window.usersettingsView = usersettingsView
