var userentitiesTable = $('#userentities-table').DataTable({
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
$('#userentities-table tbody').removeClass("d-none");
userentitiesTable.columns.adjust().draw();

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	userentitiesTable.search(value).draw();
});

$(document).on('click', '.userentity-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-userentity-name');
	var objectId = $(e.currentTarget).attr('data-userentity-id');

	bootbox.confirm({
		message: __t('Are you sure to delete userentity "%s"?', objectName),
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
				Grocy.Api.Delete('objects/userentities/' + objectId, { },
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
