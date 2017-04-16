$('#save-purchase-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#purchase-form').serializeJSON();
	delete jsonForm.barcode;

	Grocy.FetchJson('/api/get-object/products/' + jsonForm.product_id,
		function(product)
		{
			jsonForm.amount = jsonForm.amount * product.qu_factor_purchase_to_stock;

			Grocy.FetchJson('/api/helper/uniqid',
				function (uniqidResponse)
				{
					jsonForm.amount = jsonForm.amount * product.qu_factor_purchase_to_stock;
					jsonForm.stock_id = uniqidResponse.uniqid;

					Grocy.PostJson('/api/add-object/stock', jsonForm,
						function(result)
						{
							$('#product_id_text_input').focus();
							$('#product_id_text_input').val('');
							$('#product_id_text_input').trigger('change');
							$('#amount').val(1);
							$('#purchase-form').validator('validate');
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
				$('#selected-product-purchase-qu-name').text(productStatistics.quantity_unit_purchase.name);
				$('#selected-product-last-purchased').text((productStatistics.last_purchased || 'never').substring(0, 10));
				$('#selected-product-last-purchased-timeago').text($.timeago(productStatistics.last_purchased || ''));
				$('#selected-product-last-used').text((productStatistics.last_used || 'never').substring(0, 10));
				$('#selected-product-last-used-timeago').text($.timeago(productStatistics.last_used || ''));

				Grocy.EmptyElementWhenMatches('#selected-product-last-purchased-timeago', 'NaN years ago');
				Grocy.EmptyElementWhenMatches('#selected-product-last-used-timeago', 'NaN years ago');
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
	$('.datepicker').datepicker(
	{
		format: 'yyyy-mm-dd',
		startDate: '+7d',
		todayHighlight: true,
		autoclose: true,
		calendarWeeks: true,
		orientation: 'bottom auto'
	});
	$('.datepicker').val(moment().format('YYYY-MM-DD'));
	$('.datepicker').trigger('change');

	$('.combobox').combobox({ appendId: '_text_input' });
	$('#product_id_text_input').focus();
	$('#product_id_text_input').val('');
	$('#product_id_text_input').trigger('change');

	$('#purchase-form').validator();
	$('#purchase-form').validator('validate');
});
