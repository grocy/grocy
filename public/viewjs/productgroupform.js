$('#save-product-group-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#product-group-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("product-group-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/product_groups', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/productgroups');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("product-group-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/product_groups/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/productgroups');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("product-group-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#product-group-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('product-group-form');
});

$('#product-group-form input').keydown(function (event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('product-group-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-product-group-button').click();
		}
	}
});

Grocy.Components.UserfieldsForm.Load();
$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('product-group-form');
