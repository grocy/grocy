$('#save-userobject-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = {};
	jsonData.userentity_id = Grocy.EditObjectParentId;
	
	Grocy.FrontendHelpers.BeginUiBusy("userobject-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/userobjects', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/userobjects/' + Grocy.EditObjectParentName);
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("userobject-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/userobjects/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/userobjects/' + Grocy.EditObjectParentName);
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("userobject-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

Grocy.Components.UserfieldsForm.Load();
$("#userfields-form").removeClass("border").removeClass("border-info").removeClass("p-2").find("h2").addClass("d-none");
