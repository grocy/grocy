Grocy.Components.UserfieldsForm = { };

Grocy.Components.UserfieldsForm.Save = function(success, error)
{
	var jsonData = { };

	$("#userfields-form .userfield-input").each(function()
	{
		var input = $(this);
		var fieldName = input.attr("id");
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
	Grocy.Api.Get('userfields/' + $("#userfields-form").data("entity") + '/' + Grocy.EditObjectId,
		function(result)
		{
			$.each(result, function(key, value)
			{
				var input = $("#" + key + ".userfield-input");
				
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
