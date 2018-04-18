$(document).on('click', '.product-delete-button', function(e)
{
	bootbox.confirm({
		message: 'Delete product <strong>' + $(e.currentTarget).attr('data-product-name') + '</strong>?',
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Get('delete-object/products/' + $(e.currentTarget).attr('data-product-id'),
					function(result)
					{
						window.location.href = U('/products');
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		}
	});
});

$('#products-table').DataTable({
	'pageLength': 50,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});
