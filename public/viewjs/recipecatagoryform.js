$('#save-recipe-catagory-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#recipe-catagory-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("recipe-catagory-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/recipe_catagories', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				window.location.href = U('/recipecatagories');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-catagory-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/recipe_catagories/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.location.href = U('/recipecatagories');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-catagory-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#recipe-catagory-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-catagory-form');
});

$('#recipe-catagory-form input').keydown(function (event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('recipe-catagory-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-recipe-catagory-button').click();
		}
	}
});

Grocy.Components.UserfieldsForm.Load();
$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('recipe-catagory-form');
