var userentitiesTable = $('#userentities-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
});
$('#userentities-table tbody').removeClass("d-none");
userentitiesTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	userentitiesTable.search(value).draw();
}, 200));

$(document).on('click', '.userentity-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-userentity-name');
	var objectId = $(e.currentTarget).attr('data-userentity-id');

	bootbox.confirm({
		message: __t('Are you sure to delete userentity "%s"?', objectName),
		closeButton: false,
		buttons: {
			confirm: {
				label: __t('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: __t('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/userentities/' + objectId, {},
					function(result)
					{
						window.location.href = U('/userentities');
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
