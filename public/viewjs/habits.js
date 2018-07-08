$(document).on('click', '.habit-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-habit-name');
	var objectId = $(e.currentTarget).attr('data-habit-id');

	bootbox.confirm({
		message: L('Are you sure to delete habit "#1"?', objectName),
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
				Grocy.Api.Get('delete-object/habits/' + objectId,
					function(result)
					{
						window.location.href = U('/habits');
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

$('#habits-table').DataTable({
	'bPaginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});
