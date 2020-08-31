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

	$("#userfields-form .userfield-input").not("div").each(function()
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
		else if (input.attr("type") == "file")
		{
			var old_file = input.data('old-file')
			if (old_file) {
				Grocy.Api.Delete('files/userfiles/' + old_file, null, null,
					function (xhr) {
						Grocy.FrontendHelpers.ShowGenericError('Could not delete file', xhr);
					});
				jsonData[fieldName] = "";
			}
			if (input[0].files.length > 0){
				// Files service requires an extension
				var fileName = RandomString() + '.' + input[0].files[0].name.split('.').reverse()[0];

				jsonData[fieldName] = btoa(fileName) + '_' + btoa(input[0].files[0].name);
				Grocy.Api.UploadFile(input[0].files[0], 'userfiles', fileName,
					function (result)
					{
					},
					function (xhr)
					{
						Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
					}
				);
			}
			else
			{
				//jsonData[fieldName] = null;
			}
		}
		else if ($(this).hasAttr("multiple"))
		{
			jsonData[fieldName] = $(this).val().join(",");
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
				else if (input.hasAttr("multiple"))
				{
					input.val(value.split(","));
					$(".selectpicker").selectpicker("render");
				}
				if (input.attr('type') == "file")
				{
					if (value != null && !value.isEmpty()) {
						var file_name = atob(value.split('_')[1]);
						var file_src = value.split('_')[0];
						input.hide();
						var file_info = input.siblings('.userfield-file');
						file_info.removeClass('d-none');
						file_info.find('a.userfield-current-file')
							.attr('href', U('/files/userfiles/' + value))
							.text(file_name);
						file_info.find('img.userfield-current-file')
							.attr('src', U('/files/userfiles/' + value + '?force_serve_as=picture'))
						file_info.find('button.userfield-file-delete').click(
							function () {
								file_info.addClass('d-none');
								input.data('old-file', file_src);
								input.show();
							}
						);
					}
				}
				else
				{
					input.val(value);
				}
			});
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}
