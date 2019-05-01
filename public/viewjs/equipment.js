var equipmentTable = $('#equipment-table').DataTable({
	'paginate': false,
	'order': [[0, 'asc']],
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
	},
	'select': 'single',
	'initComplete': function()
	{
		this.api().row({ order: 'current' }, 0).select();
		DisplayEquipment($('#equipment-table tbody tr:eq(0)').data("equipment-id"));
	}
});
$('#equipment-table tbody').removeClass("d-none");
equipmentTable.columns.adjust().draw();

equipmentTable.on('select', function(e, dt, type, indexes)
{
	if (type === 'row')
	{
		var selectedEquipmentId = $(equipmentTable.row(indexes[0]).node()).data("equipment-id");
		DisplayEquipment(selectedEquipmentId)
	}
});

function DisplayEquipment(id)
{
	Grocy.Api.Get('objects/equipment/' + id,
		function(equipmentItem)
		{
			$(".selected-equipment-name").text(equipmentItem.name);
			$("#description-tab-content").html(equipmentItem.description);
			$(".equipment-edit-button").attr("href", U("/equipment/" + equipmentItem.id.toString()));

			if (equipmentItem.instruction_manual_file_name !== null && !equipmentItem.instruction_manual_file_name.isEmpty())
			{
				var pdfUrl = U('/api/files/equipmentmanuals/' + btoa(equipmentItem.instruction_manual_file_name));
				$("#selected-equipment-instruction-manual").attr("src", pdfUrl);
				$("#selected-equipment-instruction-manual").removeClass("d-none");
				$("#selected-equipment-has-no-instruction-manual-hint").addClass("d-none");

				$("a[href='#instruction-manual-tab']").tab("show");
				ResizeResponsiveEmbeds();
			}
			else
			{
				$("#selected-equipment-instruction-manual").addClass("d-none");
				$("#selected-equipment-has-no-instruction-manual-hint").removeClass("d-none");

				$("a[href='#description-tab']").tab("show");
			}
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

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
		message: __t('Are you sure to delete equipment "%s"?', objectName),
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
				Grocy.Api.Delete('objects/equipment/' + objectId, {},
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

$("#selectedEquipmentInstructionManualToggleFullscreenButton").on('click', function(e)
{
	$("#selectedEquipmentInstructionManualCard").toggleClass("fullscreen");
	$("#selectedEquipmentInstructionManualCard .card-header").toggleClass("fixed-top");
	$("#selectedEquipmentInstructionManualCard .card-body").toggleClass("mt-5");
	$("body").toggleClass("fullscreen-card");
	ResizeResponsiveEmbeds(true);
});

$("#selectedEquipmentDescriptionToggleFullscreenButton").on('click', function(e)
{
	$("#selectedEquipmentDescriptionCard").toggleClass("fullscreen");
	$("#selectedEquipmentDescriptionCard .card-header").toggleClass("fixed-top");
	$("#selectedEquipmentDescriptionCard .card-body").toggleClass("mt-5");
});
