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
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#habit-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('habit-form');
});

$('#habit-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('habit-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-habit-button').click();
		}
	}
});

$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('habit-form');

$('.input-group-habit-period-type').on('change', function(e)
{
	var periodType = $('#period_type').val();
	var periodDays = $('#period_days').val();

	if (periodType === 'dynamic-regular')
	{
		$('#habit-period-type-info').text(L('This means it is estimated that a new execution of this habit is tracked #1 days after the last was tracked', periodDays.toString()));
		$('#habit-period-type-info').removeClass('d-none');
	}
	else
	{
		$('#habit-period-type-info').addClass('d-none');
	}
});
