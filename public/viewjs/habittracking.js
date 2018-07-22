$('#save-habittracking-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#habittracking-form').serializeJSON();

	Grocy.Api.Get('habits/get-habit-details/' + jsonForm.habit_id,
		function (habitDetails)
		{
			Grocy.Api.Get('habits/track-habit-execution/' + jsonForm.habit_id + '?tracked_time=' + Grocy.Components.DateTimePicker.GetValue(),
				function(result)
				{
					toastr.success(L('Tracked execution of habit #1 on #2', habitDetails.habit.name, Grocy.Components.DateTimePicker.GetValue()));

					$('#habit_id').val('');
					$('#habit_id_text_input').focus();
					$('#habit_id_text_input').val('');
					Grocy.Components.DateTimePicker.SetValue(moment().format('YYYY-MM-DD HH:mm:ss'));
					$('#habit_id_text_input').trigger('change');
					Grocy.FrontendHelpers.ValidateForm('habittracking-form');
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$('#habit_id').on('change', function(e)
{
	var input = $('#habit_id_text_input').val().toString();
	$('#habit_id_text_input').val(input);
	$('#habit_id').data('combobox').refresh();

	var habitId = $(e.target).val();
	if (habitId)
	{
		Grocy.Components.HabitCard.Refresh(habitId);
		Grocy.Components.DateTimePicker.GetInputElement().focus();
	}
});

$('.combobox').combobox({
	appendId: '_text_input'
});

$('#habit_id_text_input').focus();
$('#habit_id_text_input').trigger('change');
Grocy.FrontendHelpers.ValidateForm('habittracking-form');

$('#habittracking-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('habittracking-form');
});

$('#habittracking-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('habittracking-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-habittracking-button').click();
		}
	}
});

Grocy.Components.DateTimePicker.GetInputElement().on('keypress', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('habittracking-form');
});
