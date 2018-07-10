var shoppingListTable = $('#shoppinglist-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	shoppingListTable.search(value).draw();
});

$(document).on('click', '.shoppinglist-delete-button', function (e)
{
	Grocy.Api.Get('delete-object/shopping_list/' + $(e.currentTarget).attr('data-shoppinglist-id'),
		function(result)
		{
			window.location.href = U('/shoppinglist');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '#add-products-below-min-stock-amount', function(e)
{
	Grocy.Api.Get('stock/add-missing-products-to-shoppinglist',
		function(result)
		{
			window.location.href = U('/shoppinglist');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
