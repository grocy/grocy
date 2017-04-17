$('#save-consumption-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consumption-form').serializeJSON();

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
					$('#consumption-form').validator('validate');
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
			function(productStatistics)
			{
				$('#selected-product-name').text(productStatistics.product.name);
				$('#selected-product-stock-amount').text(productStatistics.stock_amount || '0');
				$('#selected-product-stock-qu-name').text(productStatistics.quantity_unit_stock.name);
				$('#selected-product-stock-qu-name2').text(productStatistics.quantity_unit_stock.name);
				$('#selected-product-last-purchased').text((productStatistics.last_purchased || 'never').substring(0, 10));
				$('#selected-product-last-purchased-timeago').text($.timeago(productStatistics.last_purchased || ''));
				$('#selected-product-last-used').text((productStatistics.last_used || 'never').substring(0, 10));
				$('#selected-product-last-used-timeago').text($.timeago(productStatistics.last_used || ''));
				$('#amount').attr('max', productStatistics.stock_amount);

				Grocy.EmptyElementWhenMatches('#selected-product-last-purchased-timeago', 'NaN years ago');
				Grocy.EmptyElementWhenMatches('#selected-product-last-used-timeago', 'NaN years ago');

				if ((productStatistics.stock_amount || 0) === 0)
				{
					$('#product_id').val('');
					$('#product_id_text_input').val('');
					$('#product_id_text_input').addClass('has-error');
					$('#product_id_text_input').parent('.input-group').addClass('has-error');
					$('#product_id_text_input').closest('.form-group').addClass('has-error');
					$('#product-error').text('This product is not in stock.');
					$('#product-error').show();
				}
				else
				{
					$('#product_id_text_input').removeClass('has-error');
					$('#product_id_text_input').parent('.input-group').removeClass('has-error');
					$('#product_id_text_input').closest('.form-group').removeClass('has-error');
					$('#product-error').hide();
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
	$('.combobox').combobox({ appendId: '_text_input' });

	$('#amount').val(1);
	$('#product_id').val('');
	$('#product_id_text_input').focus();
	$('#product_id_text_input').val('');
	$('#product_id_text_input').trigger('change');

	$('#consumption-form').validator();
	$('#consumption-form').validator('validate');

	$('#consumption-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			if ($('#consumption-form').validator('validate').has('.has-error').length !== 0) //There is at least one validation error
			{
				event.preventDefault();
				return false;
			}
		}
	});
});
