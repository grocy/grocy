$('#save-choretracking-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#choretracking-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("choretracking-form");

	Grocy.Api.Get('chores/get-chore-details/' + jsonForm.chore_id,
		function (choreDetails)
		{
			Grocy.Api.Get('chores/track-chore-execution/' + jsonForm.chore_id + '?tracked_time=' + Grocy.Components.DateTimePicker.GetValue() + "&done_by=" + Grocy.Components.UserPicker.GetValue(),
				function(result)
				{
					Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
					toastr.success(L('Tracked execution of chore #1 on #2', choreDetails.chore.name, Grocy.Components.DateTimePicker.GetValue()) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoChoreExecution(' + result.chore_execution_id + ')"><i class="fas fa-undo"></i> ' + L("Undo") + '</a>');

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
Grocy.FrontendHelpers.ValidateForm('choretracking-form');

$('#choretracking-form input').keyup(function (event)
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
	Grocy.Api.Get('chores/undo-chore-execution/' + executionId.toString(),
		function(result)
		{
			toastr.success(L("Chore execution successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
