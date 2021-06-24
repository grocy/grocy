function usersettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	$scope("#locale").val(Grocy.UserSettings.locale);

	RefreshLocaleNumberInput();

}


window.usersettingsView = usersettingsView
