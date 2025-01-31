function SaveUserPicture(result, jsonData)
{
	var userId = Grocy.EditObjectId || result.created_object_id;
	Grocy.Components.UserfieldsForm.Save(() =>
	{
		if (jsonData.hasOwnProperty("picture_file_name") && !Grocy.DeleteUserPictureOnSave)
		{
			Grocy.Api.UploadFile($("#user-picture")[0].files[0], 'userpictures', jsonData.picture_file_name,
				(result) =>
				{
					window.location.href = U('/users');
				},
				(xhr) =>
				{
					Grocy.FrontendHelpers.EndUiBusy("user-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
				}
			);
		}
		else
		{
			window.location.href = U('/users');
		}
	});
}

$('#save-user-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("user-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#user-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("user-form");

	if ($("#user-picture")[0].files.length > 0)
	{
		jsonData.picture_file_name = RandomString() + CleanFileName($("#user-picture")[0].files[0].name);
	}

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('users', jsonData,
			(result) => SaveUserPicture(result, jsonData),
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("user-form");
				console.error(xhr);
			}
		);
	}
	else
	{
		if (Grocy.DeleteUserPictureOnSave)
		{
			jsonData.picture_file_name = null;

			Grocy.Api.DeleteFile(Grocy.UserPictureFileName, 'userpictures',
				function(result)
				{
					// Nothing to do
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("user-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
				}
			);
		}

		Grocy.Api.Put('users/' + Grocy.EditObjectId, jsonData,
			(result) => SaveUserPicture(result, jsonData),
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("user-form");
				console.error(xhr);
			}
		);
	}
});

$('#user-form input').keyup(function(event)
{
	var element = document.getElementById("password_confirm");
	if ($("#password").val() !== $("#password_confirm").val())
	{
		element.setCustomValidity("error");
	}
	else
	{
		element.setCustomValidity("");
	}

	Grocy.FrontendHelpers.ValidateForm('user-form');
});

$('#user-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('user-form'))
		{
			return false;
		}
		else
		{
			$('#save-user-button').click();
		}
	}
});

$("#user-picture").on("change", function(e)
{
	$("#user-picture-label").removeClass("d-none");
	$("#user-picture-label-none").addClass("d-none");
	$("#delete-current-user-picture-on-save-hint").addClass("d-none");
	$("#current-user-picture").addClass("d-none");
	Grocy.DeleteUserePictureOnSave = false;
});

Grocy.DeleteUserPictureOnSave = false;
$("#delete-current-user-picture-button").on("click", function(e)
{
	Grocy.DeleteUserPictureOnSave = true;
	$("#current-user-picture").addClass("d-none");
	$("#delete-current-user-picture-on-save-hint").removeClass("d-none");
	$("#user-picture-label").addClass("d-none");
	$("#user-picture-label-none").removeClass("d-none");
});

$("#change_password").click(function()
{
	$("#password").attr("disabled", !this.checked);
	$("#password_confirm").attr("disabled", !this.checked);

	setTimeout(function()
	{
		$("#password").focus();
	}, Grocy.FormFocusDelay);
});

if (GetUriParam("changepw") === "true")
{
	$("#change_password").click();
}
else
{
	setTimeout(function()
	{
		$('#username').focus();
	}, Grocy.FormFocusDelay);
}

Grocy.Components.UserfieldsForm.Load();
Grocy.FrontendHelpers.ValidateForm('user-form');
