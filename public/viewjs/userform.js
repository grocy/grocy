$('#save-user-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#user-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("user-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('users', jsonData,
			function(result)
			{
				window.location.href = U('/users');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("user-form");
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.Api.Put('users/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.location.href = U('/users');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("user-form");
				console.error(xhr);
			}
		);
	}
});

$('#user-form input').keyup(function (event)
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

$('#user-form input').keydown(function (event)
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
			$('#save-user-button').click();
		}
	}
});

if (GetUriParam("changepw") === "true")
{
	$('#password').focus();
}
else
{
	$('#username').focus();
}

Grocy.FrontendHelpers.ValidateForm('user-form');
