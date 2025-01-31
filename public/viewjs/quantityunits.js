var quantityUnitsTable = $('#quantityunits-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#quantityunits-table tbody').removeClass("d-none");
quantityUnitsTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	quantityUnitsTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	quantityUnitsTable.search("").draw();
});

$(document).on('click', '.quantityunit-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-quantityunit-name');
	var objectId = $(e.currentTarget).attr('data-quantityunit-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete quantity unit "%s"?', objectName),
		closeButton: false,
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/quantity_units/' + objectId, {},
					function(result)
					{
						window.location.href = U('/quantityunits');
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
		window.location.href = U('/quantityunits?include_disabled');
	}
	else
	{
		window.location.href = U('/quantityunits');
	}
});

if (GetUriParam('include_disabled'))
{
	$("#show-disabled").prop('checked', true);
}
