Grocy.Components.UserfieldsForm = {};

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

	var jsonData = {};

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
			var oldFile = input.data('old-file')
			if (oldFile)
			{
				Grocy.Api.Delete('files/userfiles/' + oldFile, null, null,
					function(xhr)
					{
						Grocy.FrontendHelpers.ShowGenericError('Could not delete file', xhr);
					});
				jsonData[fieldName] = "";
			}

			if (input[0].files.length > 0)
			{
				// Files service requires an extension
				var fileName = RandomString() + '.' + input[0].files[0].name.split('.').reverse()[0];

				jsonData[fieldName] = btoa(fileName) + '_' + btoa(input[0].files[0].name);
				Grocy.Api.UploadFile(input[0].files[0], 'userfiles', fileName,
					function(result)
					{
					},
					function(xhr)
					{
						// When navigating away immediately from the current page, this is maybe a false positive - so ignore this for now
						// Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
					}
				);
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
				else if (input.attr('type') == "file")
				{
					if (value != null && !value.isEmpty())
					{
						var fileName = atob(value.split('_')[1]);
						var fileSrc = value.split('_')[0];
						var formGroup = input.parent().parent().parent();

						formGroup.find("label.custom-file-label").text(fileName);
						formGroup.find(".userfield-file-show").attr('href', U('/files/userfiles/' + value));
						formGroup.find('.userfield-file-show').removeClass('d-none');
						formGroup.find('img.userfield-current-file')
							.attr('src', U('/files/userfiles/' + value + '?force_serve_as=picture&best_fit_width=250&best_fit_height=250'));
						LoadImagesLazy();

						formGroup.find('.userfield-file-delete').click(
							function()
							{
								formGroup.find("label.custom-file-label").text(__t("No file selected"));
								formGroup.find(".userfield-file-show").addClass('d-none');
								input.attr('data-old-file', fileSrc);
							}
						);

						input.on("change", function(e)
						{
							formGroup.find(".userfield-file-show").addClass('d-none');
						});
					}
				}
				else if (input.attr("data-userfield-type") == "link")
				{
					if (!value.isEmpty())
					{
						var data = JSON.parse(value);

						var formRow = input.parent().parent();
						formRow.find(".userfield-link-title").val(data.title);
						formRow.find(".userfield-link-link").val(data.link);

						input.val(value);
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

$(".userfield-link").keyup(function(e)
{
	var formRow = $(this).parent().parent();
	var title = formRow.find(".userfield-link-title").val();
	var link = formRow.find(".userfield-link-link").val();

	var value = {
		"title": title,
		"link": link
	};

	formRow.find(".userfield-input").val(JSON.stringify(value));
});
