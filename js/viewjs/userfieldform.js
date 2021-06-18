﻿import { WindowMessageBag } from '../helpers/messagebag';

$('#save-userfield-button').on('click', function(e)
{
	e.preventDefault();

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#userfield-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("userfield-form");

	var redirectUrl = U("/userfields");
	if (typeof GetUriParam("entity") !== "undefined" && !GetUriParam("entity").isEmpty())
	{
		redirectUrl = U("/userfields?entity=" + GetUriParam("entity"));
	}

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/userfields', jsonData,
			function(result)
			{
				if (GetUriParam("embedded") !== undefined)
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
				if (GetUriParam("embedded") !== undefined)
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

$('#userfield-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('userfield-form');
});

$('#userfield-form select').change(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('userfield-form');
});

$('#userfield-form input').keydown(function(event)
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
			$('#save-userfield-button').click();
		}
	}
});

$("#type").on("change", function(e)
{
	var value = $(this).val();

	if (value === "preset-list" || value === "preset-checklist")
	{
		$("#config").parent().removeClass("d-none");
		$("#config-hint").text(__t("A predefined list of values, one per line"));
	}
	else
	{
		$("#config").parent().addClass("d-none");
		$("#config-hint").text("");
	}
});

$('#entity').focus();

if (typeof GetUriParam("entity") !== "undefined" && !GetUriParam("entity").isEmpty())
{
	$("#entity").val(GetUriParam("entity"));
	$("#entity").trigger("change");
	$('#name').focus();
}

$("#type").trigger("change");
Grocy.FrontendHelpers.ValidateForm('userfield-form');
