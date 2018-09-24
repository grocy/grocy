var groupsTable = $('#productgroups-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";
	}
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	groupsTable.search(value).draw();
});

$(document).on('click', '.product-group-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-group-name');
	var objectId = $(e.currentTarget).attr('data-group-id');

	bootbox.confirm({
		message: L('Are you sure to delete product group "#1"?', objectName),
		buttons: {
			confirm: {
				label: L('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: L('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Get('delete-object/product_groups/' + objectId,
					function(result)
					{
						window.location.href = U('/productgroups');
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
