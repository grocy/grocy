import { ResizeResponsiveEmbeds } from "../helpers/embeds";

function equipmentView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (scope) => $(scope).find(scope);
	}

	var equipmentTable = $scope('#equipment-table').DataTable({
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
			DisplayEquipment($scope('#equipment-table tbody tr:eq(0)').data("equipment-id"));
		}
	});
	$scope('#equipment-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(equipmentTable);

	equipmentTable.on('select', function(e, dt, type, indexes)
	{
		if (type === 'row')
		{
			var selectedEquipmentId = $scope(equipmentTable.row(indexes[0]).node()).data("equipment-id");
			DisplayEquipment(selectedEquipmentId)
		}
	});

	function DisplayEquipment(id)
	{
		Grocy.Api.Get('objects/equipment/' + id,
			function(equipmentItem)
			{
				$scope(".selected-equipment-name").text(equipmentItem.name);
				$scope("#description-tab-content").html(equipmentItem.description);
				$scope(".equipment-edit-button").attr("href", U("/equipment/" + equipmentItem.id.toString()));

				if (equipmentItem.instruction_manual_file_name !== null && !equipmentItem.instruction_manual_file_name.isEmpty())
				{
					var pdfUrl = U('/api/files/equipmentmanuals/' + btoa(equipmentItem.instruction_manual_file_name));
					$scope("#selected-equipment-instruction-manual").attr("src", pdfUrl);
					$scope("#selectedEquipmentInstructionManualDownloadButton").attr("href", pdfUrl);
					$scope("#selected-equipment-instruction-manual").removeClass("d-none");
					$scope("#selectedEquipmentInstructionManualDownloadButton").removeClass("d-none");
					$scope("#selected-equipment-has-no-instruction-manual-hint").addClass("d-none");

					$scope("a[href='#instruction-manual-tab']").tab("show");
					ResizeResponsiveEmbeds();
				}
				else
				{
					$scope("#selected-equipment-instruction-manual").addClass("d-none");
					$scope("#selectedEquipmentInstructionManualDownloadButton").addClass("d-none");
					$scope("#selected-equipment-has-no-instruction-manual-hint").removeClass("d-none");

					$scope("a[href='#description-tab']").tab("show");
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

	$scope("#selectedEquipmentInstructionManualToggleFullscreenButton").on('click', function(e)
	{
		$scope("#selectedEquipmentInstructionManualCard").toggleClass("fullscreen");
		$scope("#selectedEquipmentInstructionManualCard .card-header").toggleClass("fixed-top");
		$scope("#selectedEquipmentInstructionManualCard .card-body").toggleClass("mt-5");
		$scope("body").toggleClass("fullscreen-card");
		ResizeResponsiveEmbeds(true);
	});

	$scope("#selectedEquipmentDescriptionToggleFullscreenButton").on('click', function(e)
	{
		$scope("#selectedEquipmentDescriptionCard").toggleClass("fullscreen");
		$scope("#selectedEquipmentDescriptionCard .card-header").toggleClass("fixed-top");
		$scope("#selectedEquipmentDescriptionCard .card-body").toggleClass("mt-5");
		$scope("body").toggleClass("fullscreen-card");
	});

}



window.equipmentView = equipmentView
