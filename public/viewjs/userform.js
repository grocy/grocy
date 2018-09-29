$('#save-user-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('users/create', $('#user-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/users');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.Api.Post('users/edit/' + Grocy.EditObjectId, $('#user-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/users');
			},
			function(xhr)
			{
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
