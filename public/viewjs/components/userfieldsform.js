Grocy.Components.UserfieldsForm = { };

Grocy.Components.UserfieldsForm.Save = function(success, error)
{
	if (!$("#userfields-form").length)
	{
		if (success)
		{
			success();
		}

		return;
	}

	var jsonData = { };

	$("#userfields-form .userfield-input").each(function()
	{
		var input = $(this);
		var fieldName = input.attr("data-userfield-name");
		var fieldValue = input.val();

		if (input.attr("type") == "checkbox")
		{
			jsonData[fieldName] = "0";
			if (input.is(":checked"))
			{
				jsonData[fieldName] = "1";
			}
		}
		else
		{
			jsonData[fieldName] = fieldValue;
		}
	});

	Grocy.Api.Put('userfields/' + $("#userfields-form").data("entity") + '/' + Grocy.EditObjectId, jsonData,
		function(result)
		{
			if (success)
			{
				success();
			}
		},
		function(xhr)
		{
			if (error)
			{
				error();
			}
		}
	);
}

Grocy.Components.UserfieldsForm.Load = function()
{
	if (!$("#userfields-form").length)
	{
		return;
	}

	Grocy.Api.Get('userfields/' + $("#userfields-form").data("entity") + '/' + Grocy.EditObjectId,
		function(result)
		{
			$.each(result, function(key, value)
			{
				var input = $(".userfield-input[data-userfield-name='" + key + "']");
				
				if (input.attr("type") == "checkbox" && value == 1)
				{
					input.prop("checked", true);
				}
				else
				{
					input.val(value);
				}
			});
		},
		function(xhr)
		{
			console.log(xhr);
		}
	);
}
