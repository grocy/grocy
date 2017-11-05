$('#save-consume-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consume-form').serializeJSON();

	var spoiled = 0;
	if ($('#spoiled').is(':checked'))
	{
		spoiled = 1;
	}

	Grocy.FetchJson('/api/stock/get-product-details/' + jsonForm.product_id,
		function (productDetails)
		{
			Grocy.FetchJson('/api/stock/consume-product/' + jsonForm.product_id + '/' + jsonForm.amount + '?spoiled=' + spoiled,
				function(result)
				{
					toastr.success('Removed ' + jsonForm.amount + ' ' + productDetails.quantity_unit_stock.name + ' of ' + productDetails.product.name + ' from stock');

					$('#amount').val(1);
					$('#product_id').val('');
					$('#product_id_text_input').focus();
					$('#product_id_text_input').val('');
					$('#product_id_text_input').trigger('change');
					$('#consume-form').validator('validate');
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
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
				$('#amount').attr('max', productDetails.stock_amount);
				$('#consume-form').validator('update');
				$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);

				Grocy.EmptyElementWhenMatches('#selected-product-last-purchased-timeago', 'NaN years ago');
				Grocy.EmptyElementWhenMatches('#selected-product-last-used-timeago', 'NaN years ago');

				if ((productDetails.stock_amount || 0) === 0)
				{
					$('#product_id').val('');
					$('#product_id_text_input').val('');
					$('#product_id_text_input').addClass('has-error');
					$('#product_id_text_input').parent('.input-group').addClass('has-error');
					$('#product_id_text_input').closest('.form-group').addClass('has-error');
					$('#product-error').text('This product is not in stock.');
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
		
		if (possibleOptionElement.length > 0)
		{
			$('#product_id').val(possibleOptionElement.val());
			$('#product_id').data('combobox').refresh();
			$('#product_id').trigger('change');
		}
	});

	$('#amount').val(1);
	$('#product_id').val('');
	$('#product_id_text_input').focus();
	$('#product_id_text_input').val('');
	$('#product_id_text_input').trigger('change');

	$('#consume-form').validator();
	$('#consume-form').validator('validate');

	$('#amount').on('focus', function(e)
	{
		if ($('#product_id_text_input').val().length === 0)
		{
			$('#product_id_text_input').focus();
		}
	});

	$('#consume-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			if ($('#consume-form').validator('validate').has('.has-error').length !== 0) //There is at least one validation error
			{
				event.preventDefault();
				return false;
			}
		}
	});
});
