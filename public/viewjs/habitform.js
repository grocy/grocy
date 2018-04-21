$('#save-habit-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/habits', $('#habit-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/habits');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/habits/' + Grocy.EditObjectId, $('#habit-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/habits');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#name').focus();
$('#habit-form').validator();
$('#habit-form').validator('validate');

$('.input-group-habit-period-type').on('change', function(e)
{
	var periodType = $('#period_type').val();
	var periodDays = $('#period_days').val();

	if (periodType === 'dynamic-regular')
	{
		$('#habit-period-type-info').text(L('This means it is estimated that a new execution of this habit is tracked #1 days after the last was tracked', periodDays.toString()));
		$('#habit-period-type-info').show();
	}
	else
	{
		$('#habit-period-type-info').hide();
	}
});
