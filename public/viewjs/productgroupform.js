$('#save-product-group-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/product_groups', $('#product-group-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/productgroups');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/product_groups/' + Grocy.EditObjectId, $('#product-group-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/productgroups');
			},
			function(xhr)
			{
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

$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('product-group-form');
