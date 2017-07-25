$(document).on('click', '.habit-delete-button', function(e)
{
	bootbox.confirm({
		message: 'Delete habit <strong>' + $(e.target).attr('data-habit-name') + '</strong>?',
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
				Grocy.FetchJson('/api/delete-object/habits/' + $(e.target).attr('data-habit-id'),
					function(result)
					{
						window.location.href = '/habits';
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
	$('#habits-table').DataTable({
		'pageLength': 50,
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 }
		]
	});
});
