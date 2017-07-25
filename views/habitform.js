$('#save-habit-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.PostJson('/api/add-object/habits', $('#habit-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/habits';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.PostJson('/api/edit-object/habits/' + Grocy.EditObjectId, $('#habit-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/habits';
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
	$('#name').focus();
	$('#habit-form').validator();
	$('#habit-form').validator('validate');
});

$('.input-group-habit-period-type').on('change', function(e)
{
	var periodType = $('#period_type').val();
	var periodDays = $('#period_days').val();

	if (periodType === 'dynamic-regular')
	{
		$('#habit-period-type-info').text('This means it is estimated that a new "execution" of this habit is tracked ' + periodDays.toString() + ' days after the last was tracked.');
		$('#habit-period-type-info').show();
	}
	else
	{
		$('#habit-period-type-info').hide();
	}
});
