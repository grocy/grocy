$('#save-recipe-include-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#recipe-include-form').serializeJSON();
	jsonData.recipe_id = Grocy.EditObjectParentId;
	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/recipes_nestings', jsonData,
			function(result)
			{
				window.location.href = U('/recipe/' + Grocy.EditObjectParentId);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/recipes_nestings/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.location.href = U('/recipe/' + Grocy.EditObjectParentId);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

Grocy.FrontendHelpers.ValidateForm('recipe-include-form');

$('#recipe-include-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-include-form');
});

$('#recipe-include-form select').change(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-include-form');
});

$('#recipe-include-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();
		
		if (document.getElementById('recipe-include-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-include-include-button').click();
		}
	}
});
