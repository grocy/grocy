$(document).on('click', '.battery-delete-button', function(e)
{
	bootbox.confirm({
		message: 'Delete battery <strong>' + $(e.target).attr('data-battery-name') + '</strong>?',
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
				Grocy.FetchJson('/api/delete-object/batteries/' + $(e.target).attr('data-battery-id'),
					function(result)
					{
						window.location.href = '/batteries';
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
	$('#batteries-table').DataTable({
		'pageLength': 50,
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 }
		]
	});
});
