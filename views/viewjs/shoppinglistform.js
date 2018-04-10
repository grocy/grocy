$('#save-shoppinglist-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.PostJson('/api/add-object/shopping_list', $('#shoppinglist-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/shoppinglist';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.PostJson('/api/edit-object/shopping_list/' + Grocy.EditObjectId, $('#shoppinglist-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/shoppinglist';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#product_id').on('change', function(e)
{
	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.FetchJson('/api/stock/get-product-details/' + productId,
			function (productDetails)
			{
				$('#selected-product-name').text(productDetails.product.name);
				$('#selected-product-stock-amount').text(productDetails.stock_amount || '0');
				$('#selected-product-stock-qu-name').text(productDetails.quantity_unit_stock.name);
				$('#selected-product-stock-qu-name2').text(productDetails.quantity_unit_stock.name);
				$('#selected-product-last-purchased').text((productDetails.last_purchased || 'never').substring(0, 10));
				$('#selected-product-last-purchased-timeago').text($.timeago(productDetails.last_purchased || ''));
				$('#selected-product-last-used').text((productDetails.last_used || 'never').substring(0, 10));
				$('#selected-product-last-used-timeago').text($.timeago(productDetails.last_used || ''));
				$('#amount_qu_unit').text(productDetails.quantity_unit_purchase.name);

				Grocy.EmptyElementWhenMatches('#selected-product-last-purchased-timeago', 'NaN years ago');
				Grocy.EmptyElementWhenMatches('#selected-product-last-used-timeago', 'NaN years ago');

				if ($('#product_id').hasClass('suppress-next-custom-validate-event'))
				{
					$('#product_id').removeClass('suppress-next-custom-validate-event');
				}
				else
				{
					Grocy.FetchJson('/api/get-objects/shopping_list',
						function (currentShoppingListItems)
						{
							if (currentShoppingListItems.filter(function (e) { return e.product_id === productId; }).length > 0)
							{
								$('#product_id').val('');
								$('#product_id_text_input').val('');
								$('#product_id_text_input').addClass('has-error');
								$('#product_id_text_input').parent('.input-group').addClass('has-error');
								$('#product_id_text_input').closest('.form-group').addClass('has-error');
								$('#product-error').text('This product is already on the shopping list.');
								$('#product-error').show();
								$('#product_id_text_input').focus();
							}
							else
							{
								$('#product_id_text_input').removeClass('has-error');
								$('#product_id_text_input').parent('.input-group').removeClass('has-error');
								$('#product_id_text_input').closest('.form-group').removeClass('has-error');
								$('#product-error').hide();

								$('#amount').focus();
							}
						},
						function(xhr)
						{
							console.error(xhr);
						}
					);
				}
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
	$('.combobox').combobox({
		appendId: '_text_input'
	});

	$('#product_id_text_input').on('change', function(e)
	{
		var input = $('#product_id_text_input').val().toString();
		var possibleOptionElement = $("#product_id option[data-additional-searchdata*='" + input + "']").first();

		if (possibleOptionElement.length > 0 && possibleOptionElement.text().length > 0) {
			$('#product_id').val(possibleOptionElement.val());
			$('#product_id').data('combobox').refresh();
			$('#product_id').trigger('change');
		}
	});
	
	$('#product_id_text_input').focus();
	$('#product_id_text_input').trigger('change');

	if (Grocy.EditMode === 'edit')
	{
		$('#product_id').addClass('suppress-next-custom-validate-event');
		$('#product_id').trigger('change');
	}
	
	$('#shoppinglist-form').validator();
	$('#shoppinglist-form').validator('validate');

	$('#amount').on('focus', function(e)
	{
		if ($('#product_id_text_input').val().length === 0)
		{
			$('#product_id_text_input').focus();
		}
	});

	$('#shoppinglist-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			if ($('#shoppinglist-form').validator('validate').has('.has-error').length !== 0) //There is at least one validation error
			{
				event.preventDefault();
				return false;
			}
		}
	});
});
