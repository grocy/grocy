var userobjectsTable = $('.userobjects-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('.userobjects-table tbody').removeClass("d-none");
userobjectsTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	userobjectsTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	userobjectsTable.search("").draw();
});

$(document).on('click', '.userobject-delete-button', function(e)
{
	var objectId = $(e.currentTarget).attr('data-userobject-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete this userobject?'),
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
				Grocy.Api.Delete('objects/userobjects/' + objectId, {},
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
