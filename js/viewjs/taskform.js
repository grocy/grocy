﻿function taskformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	import { WindowMessageBag } from '../helpers/messagebag';

	var datetimepicker = Grocy.Use("datetimepicker");
	var userfields = Grocy.Use("userfieldsform");

	$scope('#save-task-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#task-form').serializeJSON();
		jsonData.assigned_to_user_id = jsonData.user_id;
		delete jsonData.user_id;
		jsonData.due_date = datetimepicker.GetValue();

		Grocy.FrontendHelpers.BeginUiBusy("task-form");

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/tasks', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfields.Save(function()
					{
						if (Grocy.GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
						}
						else
						{
							window.location.href = U('/tasks');
						}
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("task-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			Grocy.Api.Put('objects/tasks/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					userfields.Save(function()
					{
						if (Grocy.GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
						}
						else
						{
							window.location.href = U('/tasks');
						}
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("task-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
	});

	$scope('#task-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('task-form');
	});

	$scope('#task-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('task-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-task-button').click();
			}
		}
	});

	userfields.Load();
	$scope('#name').focus();
	datetimepicker.GetInputElement().trigger('input');
	Grocy.FrontendHelpers.ValidateForm('task-form');

}
