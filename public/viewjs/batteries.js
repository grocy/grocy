var batteriesTable = $('#batteries-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	],
});
$('#batteries-table tbody').removeClass("d-none");
batteriesTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	batteriesTable.search(value).draw();
}, 200));

$(document).on('click', '.battery-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-battery-name');
	var objectId = $(e.currentTarget).attr('data-battery-id');

	bootbox.confirm({
		message: __t('Are you sure to delete battery "%s"?', objectName),
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
		closeButton: false,
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/batteries/' + objectId, { },
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
