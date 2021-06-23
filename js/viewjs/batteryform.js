import { WindowMessageBag } from '../helpers/messagebag';

function batteryformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");
	var userfieldsform = Grocy.Use("userfieldsform");

	$scope('#save-battery-button').on('click', function(e)
	{
		e.preventDefault();

		if ($(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#battery-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("battery-form");

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/batteries', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfieldsform.Save(function()
					{
						if (Grocy.GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
						}
						else
						{
							window.location.href = U('/batteries');
						}
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("battery-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			Grocy.Api.Put('objects/batteries/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					userfieldsform.Save(function()
					{
						if (Grocy.GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
						}
						else
						{
							window.location.href = U('/batteries');
						}
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("battery-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
	});

	$scope('#battery-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('battery-form');
	});

	$scope('#battery-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('battery-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$('#save-battery-button').click();
			}
		}
	});

	userfieldsform.UserfieldsForm.Load();
	$scope('#name').focus();
	Grocy.FrontendHelpers.ValidateForm('battery-form');

}
