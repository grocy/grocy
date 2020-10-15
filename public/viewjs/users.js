﻿var usersTable = $('#users-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
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
}, 200));

$(document).on('click', '.user-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-user-username');
	var objectId = $(e.currentTarget).attr('data-user-id');

	bootbox.confirm({
		message: __t('Are you sure to delete user "%s"?', objectName),
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
