var categoriesTable = $('#taskcategories-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(__t('datatables_localization')),
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
$('#taskcategories-table tbody').removeClass("d-none");
categoriesTable.columns.adjust().draw();

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	categoriesTable.search(value).draw();
});

$(document).on('click', '.task-category-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-category-name');
	var objectId = $(e.currentTarget).attr('data-category-id');

	bootbox.confirm({
		message: __t('Are you sure to delete task category "%s"?', objectName),
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
				Grocy.Api.Delete('objects/task_categories/' + objectId, {},
					function(result)
					{
						window.location.href = U('/taskcategories');
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
