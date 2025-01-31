var locationsTable = $('#shoppinglocations-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#shoppinglocations-table tbody').removeClass("d-none");
locationsTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	locationsTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	locationsTable.search("").draw();
});

$(document).on('click', '.shoppinglocation-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-shoppinglocation-name');
	var objectId = $(e.currentTarget).attr('data-shoppinglocation-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete store "%s"?', objectName),
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
				Grocy.Api.Delete('objects/shopping_locations/' + objectId, {},
					function(result)
					{
						window.location.href = U('/shoppinglocations');
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
		window.location.href = U('/shoppinglocations?include_disabled');
	}
	else
	{
		window.location.href = U('/shoppinglocations');
	}
});

if (GetUriParam('include_disabled'))
{
	$("#show-disabled").prop('checked', true);
}
