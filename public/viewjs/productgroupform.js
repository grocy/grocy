$('#save-product-group-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("product-group-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

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
					window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("product-group-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
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
					window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("product-group-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
});

$('#product-group-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('product-group-form');
});

$('#product-group-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('product-group-form'))
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
setTimeout(function()
{
	$('#name').focus();
}, Grocy.FormFocusDelay);
Grocy.FrontendHelpers.ValidateForm('product-group-form');
