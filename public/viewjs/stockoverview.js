var stockOverviewTable = $('#stock-overview-table').DataTable({
	'paginate': false,
	'order': [[3, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'visible': false, 'targets': 4 }
	],
	'language': JSON.parse(L('datatables_localization'))
});

$("#location-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	stockOverviewTable.column(4).search(value).draw();
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	stockOverviewTable.search(value).draw();
});

$(document).on('click', '.product-consume-button', function(e)
{
	var productId = $(e.currentTarget).attr('data-product-id');
	var productName = $(e.currentTarget).attr('data-product-name');
	var productQuName = $(e.currentTarget).attr('data-product-qu-name');
	var consumeAmount = $(e.currentTarget).attr('data-consume-amount');

	Grocy.Api.Get('stock/consume-product/' + productId + '/' + consumeAmount,
		function(result)
		{
			var oldAmount = parseInt($('#product-' + productId + '-amount').text());
			var newAmount = oldAmount - consumeAmount;
			if (newAmount === 0)
			{
				$('#product-' + productId + '-row').fadeOut(500, function()
				{
					$(this).remove();
				});
			}	
			else
			{
				$('#product-' + productId + '-amount').parent().effect('highlight', { }, 500);
				$('#product-' + productId + '-amount').fadeOut(500, function()
				{
					$(this).text(newAmount).fadeIn(500);
				});
				$('#product-' + productId + '-consume-all-button').attr('data-consume-amount', newAmount);
			}	

			toastr.success(L('Removed #1 #2 of #3 from stock', consumeAmount, productQuName, productName));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
