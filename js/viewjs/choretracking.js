function choretrackingView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (scope) => $(scope).find(scope);
	}

	var chorecard = Grocy.Use("chorecard");
	var datetimepicker = Grocy.Use("datetimepicker");
	Grocy.Use("userpicker");

	$scope('#save-choretracking-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonForm = $scope('#choretracking-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("choretracking-form");

		Grocy.Api.Get('chores/' + jsonForm.chore_id,
			function(choreDetails)
			{
				Grocy.Api.Post('chores/' + jsonForm.chore_id + '/execute', { 'tracked_time': datetimepicker.GetValue(), 'done_by': $scope("#user_id").val() },
					function(result)
					{
						Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
						toastr.success(__t('Tracked execution of chore %1$s on %2$s', choreDetails.chore.name, datetimepicker.GetValue()) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoChoreExecution(' + result.id + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');
						chorecard.Refresh($scope('#chore_id').val());

						$scope('#chore_id').val('');
						$scope('#chore_id_text_input').focus();
						$scope('#chore_id_text_input').val('');
						datetimepicker.SetValue(moment().format('YYYY-MM-DD HH:mm:ss'));
						$scope('#chore_id_text_input').trigger('change');
						Grocy.FrontendHelpers.ValidateForm('choretracking-form');
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
						console.error(xhr);
					}
				);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
				console.error(xhr);
			}
		);
	});

	$scope('#chore_id').on('change', function(e)
	{
		var input = $scope('#chore_id_text_input').val().toString();
		$scope('#chore_id_text_input').val(input);
		$scope('#chore_id').data('combobox').refresh();

		var choreId = $scope(e.target).val();
		if (choreId)
		{
			Grocy.Api.Get('objects/chores/' + choreId,
				function(chore)
				{
					if (chore.track_date_only == 1)
					{
						datetimepicker.ChangeFormat("YYYY-MM-DD");
						datetimepicker.SetValue(moment().format("YYYY-MM-DD"));
					}
					else
					{
						datetimepicker.ChangeFormat("YYYY-MM-DD HH:mm:ss");
						datetimepicker.SetValue(moment().format("YYYY-MM-DD HH:mm:ss"));
					}
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);

			chorecard.Refresh(choreId);
			datetimepicker.GetInputElement().focus();
			Grocy.FrontendHelpers.ValidateForm('choretracking-form');
		}
	});

	$scope('.combobox').combobox({
		appendId: '_text_input',
		bsVersion: '4'
	});

	$scope('#chore_id_text_input').focus();
	$scope('#chore_id_text_input').trigger('change');
	datetimepicker.GetInputElement().trigger('input');
	Grocy.FrontendHelpers.ValidateForm('choretracking-form');

	$scope('#choretracking-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('choretracking-form');
	});

	$scope('#choretracking-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('choretracking-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-choretracking-button').click();
			}
		}
	});

	datetimepicker.GetInputElement().on('keypress', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('choretracking-form');
	});

}


window.choretrackingView = choretrackingView
