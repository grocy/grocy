function taskssettingsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");
	
	$("#tasks_due_soon_days").val(Grocy.UserSettings.tasks_due_soon_days);
	
	RefreshLocaleNumberInput();
	
}
