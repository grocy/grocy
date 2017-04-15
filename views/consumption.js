$('#save-consumption-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consumption-form').serializeJSON();

	var spoiled = 0;
	if ($('#spoiled').is(':checked'))
	{
		spoiled = 1;
	}

	Grocy.FetchJson('/api/consume-product/' + jsonForm.product_id + '/' + jsonForm.amount + '?spoiled=' + spoiled,
		function(result)
		{
			$('#product_id').val(null);
			$('#amount').val(1);
			$('#product_name').focus();
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

	Grocy.FetchJson('/api/get-product-statistics/' + productId,
		function(productStatistics)
		{
			$('#selected-product-name').text(productStatistics.product.name);
			$('#selected-product-stock-amount').text(productStatistics.stock_amount || '0');
			$('#selected-product-stock-qu-name').text(productStatistics.quantity_unit_stock.name);
			$('#selected-product-stock-qu-name2').text(productStatistics.quantity_unit_stock.name);
			$('#selected-product-last-purchased').text(productStatistics.last_purchased || 'never');
			$('#selected-product-last-used').text(productStatistics.last_used || 'never');
			$('#amount').attr('max', productStatistics.stock_amount);
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
		startDate: '-3d',
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
