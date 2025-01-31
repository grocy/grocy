var usersTable = $('#users-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#users-table tbody').removeClass("d-none");
usersTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	usersTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	usersTable.search("").draw();
});

$(document).on('click', '.user-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-user-username');
	var objectId = $(e.currentTarget).attr('data-user-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete user "%s"?', objectName),
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
				Grocy.Api.Delete('users/' + objectId, {},
					function(result)
					{
						window.location.href = U('/users');
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
