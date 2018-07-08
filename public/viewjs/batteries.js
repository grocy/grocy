$(document).on('click', '.battery-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-battery-name');
	var objectId = $(e.currentTarget).attr('data-battery-id');

	bootbox.confirm({
		message: L('Are you sure to delete battery "#1"?', objectName),
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
				Grocy.Api.Get('delete-object/batteries/' + objectId,
					function(result)
					{
						window.location.href = U('/batteries');
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

$('#batteries-table').DataTable({
	'bPaginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});
