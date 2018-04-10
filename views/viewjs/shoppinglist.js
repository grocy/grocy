$(document).on('click', '.shoppinglist-delete-button', function(e)
{
	Grocy.FetchJson('/api/delete-object/shopping_list/' + $(e.target).attr('data-shoppinglist-id'),
		function(result)
		{
			window.location.href = '/shoppinglist';
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '#add-products-below-min-stock-amount', function(e)
{
	Grocy.FetchJson('/api/stock/add-missing-products-to-shoppinglist',
		function(result)
		{
			window.location.href = '/shoppinglist';
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(function()
{
	$('#shoppinglist-table').DataTable({
		'pageLength': 50,
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 }
		]
	});
});
