function choressettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");

	$scope("#chores_due_soon_days").val(Grocy.UserSettings.chores_due_soon_days);

	RefreshLocaleNumberInput();

}


window.choressettingsView = choressettingsView
