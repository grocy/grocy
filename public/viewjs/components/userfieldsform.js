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

	var editedUserfieldInputs = $("#userfields-form .userfield-input.is-dirty").not("div");

	if (!editedUserfieldInputs.length)
	{
		if (success)
		{
			success();
		}

		return;
	}

	editedUserfieldInputs.each(function(index, item)
	{
		var jsonData = {};
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
			if (input.hasAttr("data-old-file"))
			{
				var oldFile = input.attr("data-old-file");
				if (oldFile)
				{
					jsonData[fieldName] = "";
				}
			}

			if (input[0].files.length > 0)
			{
				// Files service requires an extension
				var newFile = RandomString() + '.' + CleanFileName(input[0].files[0].name.split('.').reverse()[0]);
				jsonData[fieldName] = btoa(newFile) + '_' + btoa(CleanFileName(input[0].files[0].name));

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

		Grocy.Api.Put('userfields/' + $("#userfields-form").data("entity") + '/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				if (typeof newFile !== 'undefined' && typeof oldFile !== 'undefined') // Delete and Upload
				{
					Grocy.Api.DeleteFile(oldFile, 'userfiles',
						function(result)
						{
							Grocy.Api.UploadFile(input[0].files[0], 'userfiles', newFile,
								function(result2)
								{
									if (success && index === editedUserfieldInputs.length - 1) // Last item
									{
										success();
									}
								},
								function(xhr)
								{
									Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
									if (error && index === editedUserfieldInputs.length - 1) // Last item
									{
										error();
									}
								}
							);
						},
						function(xhr)
						{
							Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
							if (error && index === editedUserfieldInputs.length - 1) // Last item
							{
								error();
							}
						}
					);
				}
				else if (typeof newFile !== 'undefined') // Upload only
				{
					Grocy.Api.UploadFile(input[0].files[0], 'userfiles', newFile,
						function(result2)
						{
							if (success && index === editedUserfieldInputs.length - 1) // Last item
							{
								success();
							}
						},
						function(xhr)
						{
							Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
							if (error && index === editedUserfieldInputs.length - 1) // Last item
							{
								error();
							}
						}
					);
				}
				else if (typeof oldFile !== 'undefined') // Delete only
				{
					Grocy.Api.DeleteFile(oldFile, 'userfiles',
						function(result)
						{
							if (success && index === editedUserfieldInputs.length - 1) // Last item
							{
								success();
							}
						},
						function(xhr)
						{
							Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
							if (error && index === editedUserfieldInputs.length - 1) // Last item
							{
								error();
							}
						}
					);
				}
				else // Nothing else to do
				{
					if (success && index === editedUserfieldInputs.length - 1) // Last item
					{
						success();
					}
				}
			},
			function(xhr)
			{
				if (error && index === editedUserfieldInputs.length - 1) // Last item
				{
					error();
				}
			}
		);
	});
}

Grocy.Components.UserfieldsForm.Load = function()
{
	if (!$("#userfields-form").length)
	{
		return;
	}

	if (typeof Grocy.EditObjectId == "undefined")
	{
		// Init fields by configured default values

		Grocy.Api.Get("objects/userfields?query[]=entity=" + $("#userfields-form").data("entity"),
			function(result)
			{
				$.each(result, function(key, userfield)
				{
					var input = $(".userfield-input[data-userfield-name='" + userfield.name + "']");

					if (userfield.type == "datetime" && userfield.default_value == "now")
					{
						input.val(moment().format("YYYY-MM-DD HH:mm:ss"));
					}
					else if (userfield.type == "date" && userfield.default_value == "now")
					{
						input.val(moment().format("YYYY-MM-DD"));
					}
					else if (userfield.type == "checkbox" && userfield.input_required == 1)
					{
						input.prop("indeterminate", true);
						input.on("change", function()
						{
							input.removeAttr("required");
						});
					}
				});

				$("form").each(function()
				{
					Grocy.FrontendHelpers.ValidateForm(this.id);
				});
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		// Load object field values

		Grocy.Api.Get('userfields/' + $("#userfields-form").data("entity") + '/' + Grocy.EditObjectId,
			function(result)
			{
				$.each(result, function(key, value)
				{
					var input = $(".userfield-input[data-userfield-name='" + key + "']");

					if (input.attr("type") == "checkbox")
					{
						// The required attribute for checkboxes is only relevant when creating objects
						input.removeAttr("required");
					}

					if (input.attr("type") == "checkbox" && value == 1)
					{
						input.prop("checked", true);
					}
					else if (input.hasAttr("multiple"))
					{
						if (value)
						{
							input.val(value.split(","));
						}

						$(".selectpicker").selectpicker("render");
					}
					else if (input.attr('type') == "file")
					{
						if (value)
						{
							var fileName = atob(value.split('_')[1]);
							var fileSrc = atob(value.split('_')[0]);
							var formGroup = input.parent().parent().parent();

							formGroup.find("label.custom-file-label").text(fileName);
							formGroup.find(".userfield-file-show").attr('href', U('/files/userfiles/' + value));
							formGroup.find('.userfield-file-show').removeClass('d-none');
							formGroup.find('img.userfield-current-file').attr('src', U('/files/userfiles/' + value + '?force_serve_as=picture&best_fit_width=250&best_fit_height=250'));

							formGroup.find('.userfield-file-delete').click(
								function()
								{
									formGroup.find("label.custom-file-label").text(__t("No file selected"));
									formGroup.find(".userfield-file-show").addClass('d-none');
									input.attr('data-old-file', fileSrc);
									input.addClass("is-dirty");
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
						if (value)
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

				$("form").each(function()
				{
					Grocy.FrontendHelpers.ValidateForm(this.id);
				});
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
}

Grocy.Components.UserfieldsForm.Clear = function()
{
	if (!$("#userfields-form").length)
	{
		return;
	}

	Grocy.Api.Get('objects/userfields?query[]=entity=' + $("#userfields-form").data("entity"),
		function(result)
		{
			$.each(result, function(key, userfield)
			{
				var input = $(".userfield-input[data-userfield-name='" + userfield.name + "']");

				if (input.attr("type") == "checkbox")
				{
					input.prop("checked", false);
				}
				else if (input.hasAttr("multiple"))
				{
					input.val("");
					$(".selectpicker").selectpicker("render");
				}
				else if (input.attr('type') == "file")
				{
					var formGroup = input.parent().parent().parent();

					formGroup.find("label.custom-file-label").text("");
					formGroup.find(".userfield-file-show").attr('href', U('/files/userfiles/' + value));
					formGroup.find('.userfield-file-show').removeClass('d-none');
					formGroup.find('img.userfield-current-file')
						.attr('src', U('/files/userfiles/' + value + '?force_serve_as=picture&best_fit_width=250&best_fit_height=250'));

					formGroup.find('.userfield-file-delete').click(
						function()
						{
							formGroup.find("label.custom-file-label").text(__t("No file selected"));
							formGroup.find(".userfield-file-show").addClass('d-none');
							input.attr('data-old-file', "");
						}
					);

					input.on("change", function(e)
					{
						formGroup.find(".userfield-file-show").addClass('d-none');
					});
				}
				else if (input.attr("data-userfield-type") == "link")
				{
					var formRow = input.parent().parent();
					formRow.find(".userfield-link-title").val(data.title);
					formRow.find(".userfield-link-link").val(data.link);

					input.val("");
				}
				else
				{
					input.val("");
				}
			});

			$("form").each(function()
			{
				Grocy.FrontendHelpers.ValidateForm(this.id);
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

$(".userfield-input").change(function(e)
{
	$("form").each(function()
	{
		Grocy.FrontendHelpers.ValidateForm(this.id);
	});
});

$(".userfield-input.selectpicker").on("changed.bs.select", function()
{
	$(this).addClass("is-dirty");
});
