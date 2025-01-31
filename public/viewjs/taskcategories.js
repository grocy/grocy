var categoriesTable = $('#taskcategories-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#taskcategories-table tbody').removeClass("d-none");
categoriesTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	categoriesTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	categoriesTable.search("").draw();
});

$(document).on('click', '.task-category-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-category-name');
	var objectId = $(e.currentTarget).attr('data-category-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete task category "%s"?', objectName),
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

$("#show-disabled").change(function()
{
	if (this.checked)
	{
		window.location.href = U('/taskcategories?include_disabled');
	}
	else
	{
		window.location.href = U('/taskcategories');
	}
});

if (GetUriParam('include_disabled'))
{
	$("#show-disabled").prop('checked', true);
}
