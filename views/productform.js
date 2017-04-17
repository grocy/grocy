$('#save-product-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.PostJson('/api/add-object/products', $('#product-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/products';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.PostJson('/api/edit-object/products/' + Grocy.EditObjectId, $('#product-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/products';
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
	$('#product-form').validator();
	$('#product-form').validator('validate');
});

$('#barcode').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();
		return false;
	}
});
