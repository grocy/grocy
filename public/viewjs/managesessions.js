var sessionsTable = $('#sessions-table').DataTable({
	'order': [[5, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#sessions-table tbody').removeClass("d-none");
sessionsTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function ()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	sessionsTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function ()
{
	$("#search").val("");
	sessionsTable.search("").draw();
});

$(document).on('click', '.session-delete-button', function (e)
{
	var button = $(e.currentTarget);
	var objectId = button.attr('data-session-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete the selected session?'),
		closeButton: false,
		className: "text-break",
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
		callback: function (result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/sessions/' + objectId, {},
					function (result)
					{
						animateCSS("#session-" + objectId + "-row", "fadeOut", function ()
						{
							$("#session-" + objectId + "-row").addClass("d-none").remove();
						});
					},
					function (xhr)
					{
						console.error(xhr);
					}
				);
			}
		}
	});
});
