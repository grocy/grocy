$('#stock-overview-table').DataTable({
	'pageLength': 50,
	'order': [[3, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});

$(document).on('click', '.product-consume-button', function(e)
{
	var productId = $(e.currentTarget).attr('data-product-id');
	var productName = $(e.currentTarget).attr('data-product-name');
	var productQuName = $(e.currentTarget).attr('data-product-qu-name');

	Grocy.Api.Get('stock/consume-product/' + productId + '/1',
		function(result)
		{
			var oldAmount = parseInt($('#product-' + productId + '-amount').text());
			var newAmount = oldAmount - 1;
			if (newAmount === 0)
			{
				$('#product-' + productId + '-row').remove();
			}	
			else
			{
				$('#product-' + productId + '-amount').text(newAmount);
			}	

			toastr.success('Removed 1 ' + productQuName + ' of ' + productName + ' from stock');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
