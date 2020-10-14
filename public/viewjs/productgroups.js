var groupsTable = $('#productgroups-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
});
$('#productgroups-table tbody').removeClass("d-none");
groupsTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	groupsTable.search(value).draw();
}, 200));

$(document).on('click', '.product-group-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-group-name');
	var objectId = $(e.currentTarget).attr('data-group-id');

	bootbox.confirm({
		message: __t('Are you sure to delete product group "%s"?', objectName),
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
				Grocy.Api.Delete('objects/product_groups/' + objectId, {},
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
$(window).on("message", function(e)
{
	var data = e.originalEvent.data;

	if (data.Message === "CloseAllModals")
	{
		window.location.reload();
	}
});
