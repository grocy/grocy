var choresTable = $('#chores-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
});
$('#chores-table tbody').removeClass("d-none");
choresTable.columns.adjust().draw();
$('.dataTables_scrollBody').addClass("dragscroll");
dragscroll.reset();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	choresTable.search(value).draw();
}, 200));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	choresTable.search("").draw();
});

$(document).on('click', '.chore-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-chore-name');
	var objectId = $(e.currentTarget).attr('data-chore-id');

	bootbox.confirm({
		message: __t('Are you sure to delete chore "%s"?', objectName),
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
				Grocy.Api.Delete('objects/chores/' + objectId, {},
					function(result)
					{
						window.location.href = U('/chores');
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
