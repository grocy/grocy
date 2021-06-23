function userformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (scope) => $(scope).find(scope);
	}

	var userfields = Grocy.Use("userfieldsform");

	function SaveUserPicture(result, jsonData)
	{
		userfields.Save(() =>
		{
			if (Object.prototype.hasOwnProperty.call(jsonData, "picture_file_name") && !Grocy.DeleteUserPictureOnSave)
			{
				Grocy.Api.UploadFile($scope("#user-picture")[0].files[0], 'userpictures', jsonData.picture_file_name,
					(result) =>
					{
						window.location.href = U('/users');
					},
					(xhr) =>
					{
						Grocy.FrontendHelpers.EndUiBusy("user-form");
						Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
					}
				);
			}
			else
			{
				window.location.href = U('/users');
			}
		});
	}

	$scope('#save-user-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#user-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("user-form");

		if ($scope("#user-picture")[0].files.length > 0)
		{
			var someRandomStuff = Math.random().toString(36).substring(2, 100) + Math.random().toString(36).substring(2, 100);
			jsonData.picture_file_name = someRandomStuff + $scope("#user-picture")[0].files[0].name;
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

				Grocy.Api.DeleteFile(Grocy.UserPictureFileName, 'userpictures', {},
					function(result)
					{
						// Nothing to do
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy("user-form");
						Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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

	$scope('#user-form input').keyup(function(event)
	{
		var element = document.getElementById("password_confirm");
		if ($scope("#password").val() !== $scope("#password_confirm").val())
		{
			element.setCustomValidity("error");
		}
		else
		{
			element.setCustomValidity("");
		}

		Grocy.FrontendHelpers.ValidateForm('user-form');
	});

	$scope('#user-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('user-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-user-button').click();
			}
		}
	});

	if (Grocy.GetUriParam("changepw") === "true")
	{
		$scope('#password').focus();
	}
	else
	{
		$scope('#username').focus();
	}

	$scope("#user-picture").on("change", function(e)
	{
		$scope("#user-picture-label").removeClass("d-none");
		$scope("#user-picture-label-none").addClass("d-none");
		$scope("#delete-current-user-picture-on-save-hint").addClass("d-none");
		$scope("#current-user-picture").addClass("d-none");
		Grocy.DeleteUserePictureOnSave = false;
	});

	Grocy.DeleteUserPictureOnSave = false;
	$scope("#delete-current-user-picture-button").on("click", function(e)
	{
		Grocy.DeleteUserPictureOnSave = true;
		$scope("#current-user-picture").addClass("d-none");
		$scope("#delete-current-user-picture-on-save-hint").removeClass("d-none");
		$scope("#user-picture-label").addClass("d-none");
		$scope("#user-picture-label-none").removeClass("d-none");
	});

	userfields.Load();
	Grocy.FrontendHelpers.ValidateForm('user-form');

}


window.userformView = userformView
