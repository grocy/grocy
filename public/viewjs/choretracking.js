$('#save-choretracking-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#choretracking-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("choretracking-form");

	Grocy.Api.Get('chores/' + jsonForm.chore_id,
		function (choreDetails)
		{
			Grocy.Api.Post('chores/' + jsonForm.chore_id + '/execute', { 'tracked_time': Grocy.Components.DateTimePicker.GetValue(), 'done_by': Grocy.Components.UserPicker.GetValue() },
				function(result)
				{
					Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
					toastr.success(__t('Tracked execution of chore %1$s on %2$s', choreDetails.chore.name, Grocy.Components.DateTimePicker.GetValue()) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoChoreExecution(' + result.id + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');

					$('#chore_id').val('');
					$('#chore_id_text_input').focus();
					$('#chore_id_text_input').val('');
					Grocy.Components.DateTimePicker.SetValue(moment().format('YYYY-MM-DD HH:mm:ss'));
					$('#chore_id_text_input').trigger('change');
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

$('#chore_id').on('change', function(e)
{
	var input = $('#chore_id_text_input').val().toString();
	$('#chore_id_text_input').val(input);
	$('#chore_id').data('combobox').refresh();

	var choreId = $(e.target).val();
	if (choreId)
	{
		Grocy.Api.Get('objects/chores/' + choreId,
			function(chore)
			{
				if (chore.track_date_only == 1)
				{
					Grocy.Components.DateTimePicker.ChangeFormat("YYYY-MM-DD");
					Grocy.Components.DateTimePicker.SetValue(moment().format("YYYY-MM-DD"));
				}
				else
				{
					Grocy.Components.DateTimePicker.ChangeFormat("YYYY-MM-DD HH:mm:ss");
					Grocy.Components.DateTimePicker.SetValue(moment().format("YYYY-MM-DD HH:mm:ss"));
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);

		Grocy.Components.ChoreCard.Refresh(choreId);
		Grocy.Components.DateTimePicker.GetInputElement().focus();
		Grocy.FrontendHelpers.ValidateForm('choretracking-form');
	}
});

$('.combobox').combobox({
	appendId: '_text_input',
	bsVersion: '4'
});

$('#chore_id_text_input').focus();
$('#chore_id_text_input').trigger('change');
Grocy.Components.DateTimePicker.GetInputElement().trigger('input');
Grocy.FrontendHelpers.ValidateForm('choretracking-form');

$('#choretracking-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('choretracking-form');
});

$('#choretracking-form input').keydown(function(event)
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
			$('#save-choretracking-button').click();
		}
	}
});

Grocy.Components.DateTimePicker.GetInputElement().on('keypress', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('choretracking-form');
});

function UndoChoreExecution(executionId)
{
	Grocy.Api.Post('chores/executions/' + executionId.toString() + '/undo', { },
		function(result)
		{
			toastr.success(__t("Chore execution successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
