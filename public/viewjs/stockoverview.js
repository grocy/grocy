var stockOverviewTable = $('#stock-overview-table').DataTable({
	'paginate': false,
	'order': [[3, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'visible': false, 'targets': 4 }
	],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true
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
			RefreshStatistics();
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

function RefreshStatistics()
{
	Grocy.Api.Get('stock/get-current-stock',
		function(result)
		{
			var amountSum = 0;
			result.forEach(element => {
				amountSum += parseInt(element.amount);
			});
			$("#info-current-stock").text(result.length + " " + Pluralize(result.length, L('Product'), L('Products')) + ", " + amountSum.toString() + " " + Pluralize(amountSum, L('Unit'), L('Units')));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);

	var nextXDays = $("#info-expiring-products").data("next-x-days");
	Grocy.Api.Get('stock/get-current-volatil-stock?expiring_days=' + nextXDays,
		function(result)
		{
			$("#info-expiring-products").text(Pluralize(result.expiring_products.length, L('#1 product expires within the next #2 days', result.expiring_products.length, nextXDays), L('#1 products expiring within the next #2 days', result.expiring_products.length, nextXDays)));
			$("#info-expired-products").text(Pluralize(result.expired_products.length, L('#1 product is already expired', result.expired_products.length), L('#1 products are already expired', result.expired_products.length)));
			$("#info-missing-products").text(Pluralize(result.missing_products.length, L('#1 product is below defined min. stock amount', result.missing_products.length), L('#1 products are below defined min. stock amount', result.missing_products.length)));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

RefreshStatistics();
