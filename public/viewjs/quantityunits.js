var quantityUnitsTable = $('#quantityunits-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	quantityUnitsTable.search(value).draw();
});

$(document).on('click', '.quantityunit-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-quantityunit-name');
	var objectId = $(e.currentTarget).attr('data-quantityunit-id');

	bootbox.confirm({
		message: L('Are you sure to delete quantity unit "#1"?', objectName),
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
				Grocy.Api.Get('delete-object/quantity_units/' + objectId,
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
