$('#save-habittracking-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#habittracking-form').serializeJSON();

	Grocy.FetchJson('/api/habits/get-habit-details/' + jsonForm.habit_id,
		function (habitDetails)
		{
			Grocy.FetchJson('/api/habits/track-habit/' + jsonForm.habit_id + '?tracked_time=' + $('#tracked_time').val(),
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
		Grocy.FetchJson('/api/habits/get-habit-details/' + habitId,
			function(habitDetails)
			{
				$('#selected-habit-name').text(habitDetails.habit.name);
				$('#selected-habit-last-tracked').text((habitDetails.last_tracked || 'never'));
				$('#selected-habit-last-tracked-timeago').text($.timeago(habitDetails.last_tracked || ''));
				$('#selected-habit-tracked-count').text((habitDetails.tracked_count || '0'));

				Grocy.EmptyElementWhenMatches('#selected-habit-last-tracked-timeago', 'NaN years ago');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$(function()
{
	$('.datetimepicker').datetimepicker(
	{
		format: 'YYYY-MM-DD HH:mm:ss',
		showTodayButton: true,
		calendarWeeks: true,
		maxDate: moment()
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
});

$('#tracked_time').on('change', function(e)
{
	var value = $('#tracked_time').val();
	var now = new Date();
	var centuryStart = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '00');
	var centuryEnd = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '99');

	if (value === 'x' || value === 'X') {
		value = '29991231';
	}

	if (value.length === 4 && !(Number.parseInt(value) > centuryStart && Number.parseInt(value) < centuryEnd))
	{
		value = (new Date()).getFullYear().toString() + value;
	}

	if (value.length === 8 && $.isNumeric(value))
	{
		value = value.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3');
		$('#tracked_time').val(value);
		$('#habittracking-form').validator('validate');
	}
});

$('#tracked_time').on('keypress', function(e)
{
	var element = $(e.target);
	var value = element.val();
	var dateObj = moment(element.val(), 'YYYY-MM-DD', true);

	$('.datepicker').datepicker('hide');

	//If input is empty and any arrow key is pressed, set date to today
	if (value.length === 0 && (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 37 || e.keyCode === 39))
	{
		dateObj = moment(new Date(), 'YYYY-MM-DD', true);
	}

	if (dateObj.isValid())
	{
		if (e.keyCode === 38) //Up
		{
			element.val(dateObj.add(-1, 'days').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 40) //Down
		{
			element.val(dateObj.add(1, 'days').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 37) //Left
		{
			element.val(dateObj.add(-1, 'weeks').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 39) //Right
		{
			element.val(dateObj.add(1, 'weeks').format('YYYY-MM-DD'));
		}
	}

	$('#habittracking-form').validator('validate');
});
