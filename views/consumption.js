$('#save-consumption-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consumption-form').serializeJSON();

	var spoiled = 0;
	if ($('#spoiled').is(':checked'))
	{
		spoiled = 1;
	}

	Grocy.FetchJson('/api/stock/consume-product/' + jsonForm.product_id + '/' + jsonForm.amount + '?spoiled=' + spoiled,
		function(result)
		{
			$('#product_id_text_input').focus();
			$('#product_id_text_input').val('');
			$('#product_id_text_input').trigger('change');
			$('#amount').val(1);
			$('#consumption-form').validator('validate');
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
		startDate: '-3d',
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

	$('#consumption-form').validator();
	$('#consumption-form').validator('validate');
});
