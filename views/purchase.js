$('#save-purchase-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#purchase-form').serializeJSON();
	delete jsonForm.barcode;

	Grocy.FetchJson('/api/get-object/products/' + jsonForm.product_id,
		function(product)
		{
			jsonForm.amount = jsonForm.amount * product.qu_factor_purchase_to_stock;

			Grocy.PostJson('/api/add-object/stock', jsonForm,
				function(result)
				{
					$('#product_id').val(null);
					$('#amount').val(1);
					$('#product_id').focus();
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
});

$('#product_id').on('change', function(e)
{
	var productId = $(e.target).val();

	Grocy.FetchJson('/api/get-product-statistics/' + productId,
		function(productStatistics)
		{
			$('#selected-product-name').text(productStatistics.product.name);
			$('#selected-product-stock-amount').text(productStatistics.stock_amount || '0');
			$('#selected-product-stock-qu-name').text(productStatistics.quantity_unit_stock.name);
			$('#selected-product-purchase-qu-name').text(productStatistics.quantity_unit_purchase.name);
			$('#selected-product-last-purchased').text(productStatistics.last_purchased || 'never');
			$('#selected-product-last-used').text(productStatistics.last_used || 'never');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
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

	$('.combobox').combobox();
	$('#product_id').focus();
	$('#product_id').val(null);
	$('#product_name').trigger('change');
	$('#purchase-form').validator();
	$('#purchase-form').validator('validate');
});
