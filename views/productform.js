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
	$('#qu_factor_purchase_to_stock').trigger('change');
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

$('.input-group-qu').on('change', function(e)
{
	var factor = $('#qu_factor_purchase_to_stock').val();
	if (factor > 1)
	{
		$('#qu-conversion-info').text('This means 1 ' + $("#qu_id_purchase option:selected").text() + ' purchased will be converted into ' + (1 * factor).toString() + ' ' + $("#qu_id_stock option:selected").text() + ' in stock.');
		$('#qu-conversion-info').show();
	}
	else
	{
		$('#qu-conversion-info').hide();
	}
});
