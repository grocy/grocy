$(document).on('click', '.location-delete-button', function(e)
{
	bootbox.confirm({
		message: L('Are you sure to delete location "#1"?', $(e.currentTarget).attr('data-location-name')),
		buttons: {
			confirm: {
				label: L('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: L('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Get('delete-object/locations/' + $(e.currentTarget).attr('data-location-id'),
					function(result)
					{
						window.location.href = U('/locations');
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

$('#locations-table').DataTable({
	'pageLength': 50,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});
