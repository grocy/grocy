var groupsTable = $('#productgroups-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
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
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	groupsTable.search("").draw();
});

$(document).on('click', '.product-group-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-group-name');
	var objectId = $(e.currentTarget).attr('data-group-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete product group "%s"?', objectName),
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

	if (data.Message === "CloseLastModal")
	{
		window.location.reload();
	}
});

$("#show-disabled").change(function()
{
	if (this.checked)
	{
		window.location.href = U('/productgroups?include_disabled');
	}
	else
	{
		window.location.href = U('/productgroups');
	}
});

if (GetUriParam('include_disabled'))
{
	$("#show-disabled").prop('checked', true);
}
