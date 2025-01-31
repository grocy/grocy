$('#save-quconversion-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("quconversion-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#quconversion-form').serializeJSON();
	jsonData.from_qu_id = $("#from_qu_id").val();
	Grocy.FrontendHelpers.BeginUiBusy("quconversion-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/quantity_unit_conversions', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (typeof GetUriParam("qu-unit") !== "undefined")
					{
						if (GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
						}
						else
						{
							window.location.href = U("/quantityunit/" + GetUriParam("qu-unit"));
						}
					}
					else
					{
						window.parent.postMessage(WindowMessageBag("ProductQUConversionChanged"), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("quconversion-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/quantity_unit_conversions/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (typeof GetUriParam("qu-unit") !== "undefined")
					{
						if (GetUriParam("embedded") !== undefined)
						{
							window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
						}
						else
						{
							window.location.href = U("/quantityunit/" + GetUriParam("qu-unit"));
						}
					}
					else
					{
						window.parent.postMessage(WindowMessageBag("ProductQUConversionChanged"), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("quconversion-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
});

$('#quconversion-form input').keyup(function(event)
{
	$('.input-group-qu').trigger('change');
	Grocy.FrontendHelpers.ValidateForm('quconversion-form');
});

$('#quconversion-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('quconversion-form'))
		{
			return false;
		}
		else
		{
			$('#save-quconversion-button').click();
		}
	}
});

$('.input-group-qu').on('change', function(e)
{
	var fromQuId = $("#from_qu_id").val();
	var toQuId = $("#to_qu_id").val();
	var factor = Number.parseFloat($('#factor').val());

	if (fromQuId == toQuId)
	{
		var validationMessage = __t('This cannot be equal to %s', $("#from_qu_id option:selected").text());
		$("#to_qu_id").parent().find(".invalid-feedback").text(validationMessage);
		$("#to_qu_id")[0].setCustomValidity(validationMessage);
	}
	else
	{
		$("#to_qu_id")[0].setCustomValidity("");
	}

	if (fromQuId && toQuId)
	{
		$('#qu-conversion-info').text(__t('This means 1 %1$s is the same as %2$s %3$s', $("#from_qu_id option:selected").text(), (1.0 * factor).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }), __n((1.0 * factor).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }), $("#to_qu_id option:selected").text(), $("#to_qu_id option:selected").data("plural-form"), true)));
		$('#qu-conversion-info').removeClass('d-none');
		$('#qu-conversion-inverse-info').removeClass('d-none');
		$('#qu-conversion-inverse-info').text(__t('This means 1 %1$s is the same as %2$s %3$s', $("#to_qu_id option:selected").text(), (1.0 / factor).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }), __n((1.0 / factor), $("#from_qu_id option:selected").text(), $("#from_qu_id option:selected").data("plural-form"), true)));
	}
	else
	{
		$('#qu-conversion-info').addClass('d-none');
		$('#qu-conversion-inverse-info').addClass('d-none');
	}

	Grocy.FrontendHelpers.ValidateForm('quconversion-form');
});

Grocy.Components.UserfieldsForm.Load();
$('.input-group-qu').trigger('change');
Grocy.FrontendHelpers.ValidateForm('quconversion-form');
setTimeout(function()
{
	$('#from_qu_id').focus();
}, Grocy.FormFocusDelay);

if (GetUriParam("qu-unit") !== undefined)
{
	$("#from_qu_id").attr("disabled", "");
}
