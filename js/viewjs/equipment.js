import { ResizeResponsiveEmbeds } from "../helpers/embeds";

var equipmentTable = $('#equipment-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs),
	select: {
		style: 'single',
		selector: 'tr td:not(:first-child)'
	},
	'initComplete': function()
	{
		this.api().row({ order: 'current' }, 0).select();
		DisplayEquipment($('#equipment-table tbody tr:eq(0)').data("equipment-id"));
	}
});
$('#equipment-table tbody').removeClass("d-none");
Grocy.FrontendHelpers.InitDataTable(equipmentTable);

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
				$("#selectedEquipmentInstructionManualDownloadButton").attr("href", pdfUrl);
				$("#selected-equipment-instruction-manual").removeClass("d-none");
				$("#selectedEquipmentInstructionManualDownloadButton").removeClass("d-none");
				$("#selected-equipment-has-no-instruction-manual-hint").addClass("d-none");

				$("a[href='#instruction-manual-tab']").tab("show");
				ResizeResponsiveEmbeds();
			}
			else
			{
				$("#selected-equipment-instruction-manual").addClass("d-none");
				$("#selectedEquipmentInstructionManualDownloadButton").addClass("d-none");
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

Grocy.FrontendHelpers.MakeDeleteConfirmBox(
	'Are you sure to delete equipment "%s"?',
	'.equipment-delete-button',
	'data-equipment-name',
	'data-equipment-id',
	'objects/equipment/',
	'/equipment'
);

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
	$("body").toggleClass("fullscreen-card");
});
