function batterytrackingView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var batterycard = Grocy.Use("batterycard");
	var datetimepicker = Grocy.Use("datetimepicker");

	$scope('#save-batterytracking-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonForm = $scope('#batterytracking-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("batterytracking-form");

		Grocy.Api.Get('batteries/' + jsonForm.battery_id,
			function(batteryDetails)
			{
				Grocy.Api.Post('batteries/' + jsonForm.battery_id + '/charge', { 'tracked_time': $scope('#tracked_time').find('input').val() },
					function(result)
					{
						Grocy.FrontendHelpers.EndUiBusy("batterytracking-form");
						toastr.success(__t('Tracked charge cycle of battery %1$s on %2$s', batteryDetails.battery.name, $scope('#tracked_time').find('input').val()) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoChargeCycle(' + result.id + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');
						batterycard.Refresh($('#battery_id').val());

						$scope('#battery_id').val('');
						$scope('#battery_id_text_input').focus();
						$scope('#battery_id_text_input').val('');
						$scope('#tracked_time').find('input').val(moment().format('YYYY-MM-DD HH:mm:ss'));
						$scope('#tracked_time').find('input').trigger('change');
						$scope('#battery_id_text_input').trigger('change');
						Grocy.FrontendHelpers.ValidateForm('batterytracking-form');
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy("batterytracking-form");
						console.error(xhr);
					}
				);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("batterytracking-form");
				console.error(xhr);
			}
		);
	});

	$scope('#battery_id').on('change', function(e)
	{
		var input = $scope('#battery_id_text_input').val().toString();
		$scope('#battery_id_text_input').val(input);
		$scope('#battery_id').data('combobox').refresh();

		var batteryId = $scope(e.target).val();
		if (batteryId)
		{
			batterycard.Refresh(batteryId);
			$scope('#tracked_time').find('input').focus();
			Grocy.FrontendHelpers.ValidateForm('batterytracking-form');
		}
	});

	$scope('.combobox').combobox({
		appendId: '_text_input',
		bsVersion: '4'
	});

	$scope('#battery_id').val('');
	$scope('#battery_id_text_input').focus();
	$scope('#battery_id_text_input').val('');
	$scope('#battery_id_text_input').trigger('change');
	datetimepicker.GetInputElement().trigger('input');
	Grocy.FrontendHelpers.ValidateForm('batterytracking-form');

	$scope('#batterytracking-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('batterytracking-form');
	});

	$scope('#batterytracking-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('batterytracking-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-batterytracking-button').click();
			}
		}
	});

	$scope('#tracked_time').find('input').on('keypress', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('batterytracking-form');
	});
}
