$(document).on('click', '.product-delete-button', function(e)
{
	bootbox.confirm({
		message: 'Delete product <strong>' + $(e.target).attr('data-product-name') + '</strong>?',
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
			if (result == true)
			{
				Grocy.FetchJson('/api/delete-object/products/' + $(e.target).attr('data-product-id'),
					function(result)
					{
						window.location.href = '/products';
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

$(function()
{
	$('#products-table').DataTable({
		'pageLength': 50,
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 }
		]
	});
});
