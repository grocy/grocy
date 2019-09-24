var userobjectsTable = $('#userobjects-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': IsJsonString(__t('datatables_localization')) ? JSON.parse(__t('datatables_localization')) : { },
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	}
});
$('#userobjects-table tbody').removeClass("d-none");
userobjectsTable.columns.adjust().draw();

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	userobjectsTable.search(value).draw();
});

$(document).on('click', '.userobject-delete-button', function (e)
{
	var objectId = $(e.currentTarget).attr('data-userobject-id');

	bootbox.confirm({
		message: __t('Are you sure to delete this userobject?'),
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
				Grocy.Api.Delete('objects/userobjects/' + objectId, { },
					function(result)
					{
						window.location.reload();
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
