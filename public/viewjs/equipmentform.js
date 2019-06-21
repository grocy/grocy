$('#save-equipment-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#equipment-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("equipment-form");

	if ($("#instruction-manual")[0].files.length > 0)
	{
		var someRandomStuff = Math.random().toString(36).substring(2, 100) + Math.random().toString(36).substring(2, 100);
		jsonData.instruction_manual_file_name = someRandomStuff + $("#instruction-manual")[0].files[0].name;
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
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (jsonData.hasOwnProperty("instruction_manual_file_name") && !Grocy.DeleteInstructionManualOnSave)
					{
						Grocy.Api.UploadFile($("#instruction-manual")[0].files[0], 'equipmentmanuals', jsonData.instruction_manual_file_name,
							function (result)
							{
								window.location.href = U('/equipment');
							},
							function (xhr)
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
		};

		Grocy.Api.Put('objects/equipment/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (jsonData.hasOwnProperty("instruction_manual_file_name") && !Grocy.DeleteInstructionManualOnSave)
					{
						Grocy.Api.UploadFile($("#instruction-manual")[0].files[0], 'equipmentmanuals', jsonData.instruction_manual_file_name,
							function(result)
							{
								window.location.href = U('/equipment');;
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
						window.location.href = U('/equipment');;
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

$('#equipment-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('equipment-form');
});

$('#equipment-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('equipment-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-equipment-button').click();
		}
	}
});

Grocy.DeleteInstructionManualOnSave = false;
$('#delete-current-instruction-manual-button').on('click', function (e)
{
	Grocy.DeleteInstructionManualOnSave = true;
	$("#current-equipment-instruction-manual").addClass("d-none");
	$("#delete-current-instruction-manual-on-save-hint").removeClass("d-none");
	$("#delete-current-instruction-manual-button").addClass("disabled");
});

$('#description').summernote({
	minHeight: '300px',
	lang: __t('summernote_locale')
});

ResizeResponsiveEmbeds();

Grocy.Components.UserfieldsForm.Load();
$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('equipment-form');
