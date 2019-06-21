var usersTable = $('#users-table').DataTable({
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
$('#users-table tbody').removeClass("d-none");
usersTable.columns.adjust().draw();

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	usersTable.search(value).draw();
});

$(document).on('click', '.user-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-user-username');
	var objectId = $(e.currentTarget).attr('data-user-id');

	bootbox.confirm({
		message: __t('Are you sure to delete user "%s"?', objectName),
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
