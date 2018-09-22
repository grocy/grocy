$('#save-chore-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/chores', $('#chore-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/chores');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/chores/' + Grocy.EditObjectId, $('#chore-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/chores');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#chore-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('chore-form');
});

$('#chore-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('chore-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-chore-button').click();
		}
	}
});

$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('chore-form');

$('.input-group-chore-period-type').on('change', function(e)
{
	var periodType = $('#period_type').val();
	var periodDays = $('#period_days').val();

	if (periodType === 'dynamic-regular')
	{
		$('#chore-period-type-info').text(L('This means it is estimated that a new execution of this chore is tracked #1 days after the last was tracked', periodDays.toString()));
		$('#chore-period-type-info').removeClass('d-none');
	}
	else
	{
		$('#chore-period-type-info').addClass('d-none');
	}
});
