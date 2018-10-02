var equipmentTable = $('#equipment-table').DataTable({
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

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	},
	'select': 'single',
	'initComplete': function()
	{
		this.api().row({ order: 'current' }, 0).select();
	}
});

equipmentTable.on('select', function(e, dt, type, indexes)
{
	if (type === 'row')
	{
		var selectedEquipmentId = $(equipmentTable.row(indexes[0]).node()).data("equipment-id");
		console.log(selectedEquipmentId);
	}
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	equipmentTable.search(value).draw();
});

$(document).on('click', '.equipment-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-equipment-name');
	var objectId = $(e.currentTarget).attr('data-equipment-id');

	bootbox.confirm({
		message: L('Are you sure to delete equipment "#1"?', objectName),
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
				Grocy.Api.Get('delete-object/equipment/' + objectId,
					function(result)
					{
						window.location.href = U('/equipment');
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
