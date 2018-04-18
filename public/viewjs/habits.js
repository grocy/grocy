$(document).on('click', '.habit-delete-button', function(e)
{
	bootbox.confirm({
		message: 'Delete habit <strong>' + $(e.currentTarget).attr('data-habit-name') + '</strong>?',
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
				Grocy.Api.Get('delete-object/habits/' + $(e.currentTarget).attr('data-habit-id'),
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
	'pageLength': 50,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});
