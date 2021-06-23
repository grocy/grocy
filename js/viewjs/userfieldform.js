import { WindowMessageBag } from '../helpers/messagebag';

function userfieldformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");

	$scope('#save-userfield-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#userfield-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("userfield-form");

		var redirectUrl = U("/userfields");
		if (typeof Grocy.GetUriParam("entity") !== "undefined" && !Grocy.GetUriParam("entity").isEmpty())
		{
			redirectUrl = U("/userfields?entity=" + Grocy.GetUriParam("entity"));
		}

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/userfields', jsonData,
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
					Grocy.FrontendHelpers.EndUiBusy("userfield-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			Grocy.Api.Put('objects/userfields/' + Grocy.EditObjectId, jsonData,
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
					Grocy.FrontendHelpers.EndUiBusy("userfield-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
	});

	$scope('#userfield-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('userfield-form');
	});

	$scope('#userfield-form select').change(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('userfield-form');
	});

	$scope('#userfield-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('userfield-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-userfield-button').click();
			}
		}
	});

	$scope("#type").on("change", function(e)
	{
		var value = $(this).val();

		if (value === "preset-list" || value === "preset-checklist")
		{
			$scope("#config").parent().removeClass("d-none");
			$scope("#config-hint").text(__t("A predefined list of values, one per line"));
		}
		else
		{
			$scope("#config").parent().addClass("d-none");
			$scope("#config-hint").text("");
		}
	});

	$scope('#entity').focus();

	if (typeof Grocy.GetUriParam("entity") !== "undefined" && !Grocy.GetUriParam("entity").isEmpty())
	{
		$scope("#entity").val(Grocy.GetUriParam("entity"));
		$scope("#entity").trigger("change");
		$scope('#name').focus();
	}

	$scope("#type").trigger("change");
	Grocy.FrontendHelpers.ValidateForm('userfield-form');

}

window.userfieldformView = userfieldformView;