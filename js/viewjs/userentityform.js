import { WindowMessageBag } from '../helpers/messagebag';

function userentityformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	$scope('#save-userentity-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#userentity-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("userentity-form");

		var redirectUrl = U("/userentities");

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/userentities', jsonData,
				function(result)
				{
					if (Grocy.GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
					}
					else
					{
						window.location.href = redirectUrl;
					}
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("userentity-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			Grocy.Api.Put('objects/userentities/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					if (Grocy.GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
					}
					else
					{
						window.location.href = redirectUrl;
					}
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("userentity-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
	});

	$scope('#userentity-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('userentity-form');
	});

	$scope('#userentity-form select').change(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('userentity-form');
	});

	$scope('#userentity-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if ($scope('#userentity-form')[0].checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-userentity-button').click();
			}
		}
	});

	$scope("#show_in_sidebar_menu").on("click", function()
	{
		if (this.checked)
		{
			$scope("#icon_css_class").removeAttr("disabled");
		}
		else
		{
			$scope("#icon_css_class").attr("disabled", "");
		}
	});

	$scope('#name').focus();
	Grocy.FrontendHelpers.ValidateForm('userentity-form');

	// Click twice to trigger on-click but not change the actual checked state
	$scope("#show_in_sidebar_menu").click();
	$scope("#show_in_sidebar_menu").click();

}



window.userentityformView = userentityformView
