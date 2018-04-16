$('#save-habittracking-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#habittracking-form').serializeJSON();

	Grocy.FetchJson('/api/habits/get-habit-details/' + jsonForm.habit_id,
		function (habitDetails)
		{
			Grocy.FetchJson('/api/habits/track-habit-execution/' + jsonForm.habit_id + '?tracked_time=' + $('#tracked_time').val(),
				function(result)
				{
					toastr.success('Tracked execution of habit ' + habitDetails.habit.name + ' on ' + $('#tracked_time').val());

					$('#habit_id').val('');
					$('#habit_id_text_input').focus();
					$('#habit_id_text_input').val('');
					$('#tracked_time').val(moment().format('YYYY-MM-DD HH:mm:ss'));
					$('#tracked_time').trigger('change');
					$('#habit_id_text_input').trigger('change');
					$('#habittracking-form').validator('validate');
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
	var habitId = $(e.target).val();

	if (habitId)
	{
		Grocy.Components.HabitCard.Refresh(habitId);
		$('#tracked_time').focus();
	}
});

$('#tracked_time').val(moment().format('YYYY-MM-DD HH:mm:ss'));
$('#tracked_time').trigger('change');

$('#tracked_time').on('focus', function(e)
{
	if ($('#habit_id_text_input').val().length === 0)
	{
		$('#habit_id_text_input').focus();
	}
});

$('.combobox').combobox({
	appendId: '_text_input'
});

$('#habit_id').val('');
$('#habit_id_text_input').focus();
$('#habit_id_text_input').val('');
$('#habit_id_text_input').trigger('change');

$('#habittracking-form').validator();
$('#habittracking-form').validator('validate');

$('#habittracking-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if ($('#habittracking-form').validator('validate').has('.has-error').length !== 0) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
	}
});

$('#tracked_time').on('keypress', function(e)
{
	$('#habittracking-form').validator('validate');
});
