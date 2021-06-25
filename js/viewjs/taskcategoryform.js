import { WindowMessageBag } from '../helpers/messagebag';

function taskcategoryformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	var userfields = Grocy.Use("userfieldsform");

	$scope('#save-task-category-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#task-category-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("task-category-form");

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/task_categories', jsonData,
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
							window.location.href = U('/taskcategories');
						}
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("task-category-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			Grocy.Api.Put('objects/task_categories/' + Grocy.EditObjectId, jsonData,
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
							window.location.href = U('/taskcategories');
						}
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("task-category-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
	});

	$scope('#task-category-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('task-category-form');
	});

	$scope('#task-category-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if ($scope('#task-category-form')[0].checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-task-category-button').click();
			}
		}
	});

	userfields.Load();
	$scope('#name').focus();
	Grocy.FrontendHelpers.ValidateForm('task-category-form');

}


window.taskcategoryformView = taskcategoryformView
