import { RandomString } from '../helpers/extensions';
import { ResizeResponsiveEmbeds } from '../helpers/embeds';

function equipmentformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	var userfields = Grocy.Use("userfieldsform");

	$scope('#save-equipment-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#equipment-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("equipment-form");

		if ($scope("#instruction-manual")[0].files.length > 0)
		{
			var someRandomStuff = RandomString();
			jsonData.instruction_manual_file_name = someRandomStuff + $scope("#instruction-manual")[0].files[0].name;
		}

		if (Grocy.DeleteInstructionManualOnSave)
		{
			jsonData.instruction_manual_file_name = null;
		}

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/equipment', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfields.Save(function()
					{
						// https://eslint.org/docs/rules/no-prototype-builtins
						if (Object.prototype.hasOwnProperty.call(jsonData, "instruction_manual_file_name") && !Grocy.DeleteInstructionManualOnSave)
						{
							Grocy.Api.UploadFile($scope("#instruction-manual")[0].files[0], 'equipmentmanuals', jsonData.instruction_manual_file_name,
								function(result)
								{
									window.location.href = U('/equipment');
								},
								function(xhr)
								{
									Grocy.FrontendHelpers.EndUiBusy("equipment-form");
									Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
								}
							);
						}
						else
						{
							window.location.href = U('/equipment');
						}
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("equipment-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			if (Grocy.DeleteInstructionManualOnSave)
			{
				Grocy.Api.DeleteFile(Grocy.InstructionManualFileNameName, 'equipmentmanuals', {},
					function(result)
					{
						// Nothing to do
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy("equipment-form");
						Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
					}
				);
			}

			Grocy.Api.Put('objects/equipment/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					userfields.Save(function()
					{
						if (Object.prototype.hasOwnProperty.call(jsonData, "instruction_manual_file_name") && !Grocy.DeleteInstructionManualOnSave)
						{
							Grocy.Api.UploadFile($scope("#instruction-manual")[0].files[0], 'equipmentmanuals', jsonData.instruction_manual_file_name,
								function(result)
								{
									window.location.href = U('/equipment');
								},
								function(xhr)
								{
									Grocy.FrontendHelpers.EndUiBusy("equipment-form");
									Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
								}
							);
						}
						else
						{
							window.location.href = U('/equipment');
						}
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("equipment-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
	});

	$scope('#equipment-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('equipment-form');
	});

	$scope('#equipment-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if ($scope('#equipment-form')[0].checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-equipment-button').click();
			}
		}
	});

	Grocy.DeleteInstructionManualOnSave = false;
	$scope('#delete-current-instruction-manual-button').on('click', function(e)
	{
		Grocy.DeleteInstructionManualOnSave = true;
		$scope("#current-equipment-instruction-manual").addClass("d-none");
		$scope("#delete-current-instruction-manual-on-save-hint").removeClass("d-none");
		$scope("#delete-current-instruction-manual-button").addClass("disabled");
		$scope("#instruction-manual-label").addClass("d-none");
		$scope("#instruction-manual-label-none").removeClass("d-none");
	});

	ResizeResponsiveEmbeds();
	$scope("embed").attr("src", $scope("embed").data("src"));

	userfields.Load();
	$scope('#name').focus();
	Grocy.FrontendHelpers.ValidateForm('equipment-form');

	$scope("#instruction-manual").on("change", function(e)
	{
		$scope("#instruction-manual-label").removeClass("d-none");
		$scope("#instruction-manual-label-none").addClass("d-none");
		$scope("#delete-current-instruction-manual-on-save-hint").addClass("d-none");
		$scope("#current-instruction-manuale").addClass("d-none");
		Grocy.DeleteProductPictureOnSave = false;
	});

}


window.equipmentformView = equipmentformView
