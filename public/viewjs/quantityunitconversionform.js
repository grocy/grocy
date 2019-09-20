$('#save-quconversion-button').on('click', function(e)
{
	e.preventDefault();

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
						window.location.href = U("/quantityunit/" + GetUriParam("qu-unit"));
					}
					else
					{
						window.location.href = U("/product/" + GetUriParam("product"));
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("quconversion-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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
						window.location.href = U("/quantityunit/" + GetUriParam("qu-unit"));
					}
					else
					{
						window.location.href = U("/product/" + GetUriParam("product"));
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("quconversion-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('quconversion-form').checkValidity() === false) //There is at least one validation error
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
	var factor = $('#factor').val();

	if (fromQuId == toQuId)
	{
		$("#to_qu_id").parent().find(".invalid-feedback").text(__t('This cannot be equal to %s', $("#from_qu_id option:selected").text()));
		$("#to_qu_id")[0].setCustomValidity("error");
	}
	else
	{
		$("#to_qu_id")[0].setCustomValidity("");
	}

	if (fromQuId && toQuId)
	{
		$('#qu-conversion-info').text(__t('This means 1 %1$s is the same as %2$s %3$s', $("#from_qu_id option:selected").text(), (1 * factor).toString(), __n((1 * factor).toString(), $("#to_qu_id option:selected").text(), $("#to_qu_id option:selected").data("plural-form"))));
		$('#qu-conversion-info').removeClass('d-none');
	}
	else
	{
		$('#qu-conversion-info').addClass('d-none');
	}

	Grocy.FrontendHelpers.ValidateForm('quconversion-form');
});

Grocy.Components.UserfieldsForm.Load();
$('.input-group-qu').trigger('change');
$('#from_qu_id').focus();
Grocy.FrontendHelpers.ValidateForm('quconversion-form');
