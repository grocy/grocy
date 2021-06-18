
class GrocyFrontendHelpers
{
	constructor(Grocy, Api)
	{
		this.Grocy = Grocy;
		this.Api = Api;
	}

	ValidateForm(formId)
	{
		var form = document.getElementById(formId);
		if (form === null || form === undefined)
		{
			return;
		}

		if (form.checkValidity() === true)
		{
			$(form).find(':submit').removeClass('disabled');
		}
		else
		{
			$(form).find(':submit').addClass('disabled');
		}

		$(form).addClass('was-validated');
	}

	BeginUiBusy(formId = null)
	{
		$("body").addClass("cursor-busy");

		if (formId !== null)
		{
			$("#" + formId + " :input").attr("disabled", true);
		}
	}

	EndUiBusy(formId = null)
	{
		$("body").removeClass("cursor-busy");

		if (formId !== null)
		{
			$("#" + formId + " :input").attr("disabled", false);
		}
	}

	ShowGenericError(message, exception)
	{
		toastr.error(this.Grocy.translate(message) + '<br><br>' + this.Grocy.translate('Click to show technical details'), '', {
			onclick: function()
			{
				bootbox.alert({
					title: this.Grocy.translate('Error details'),
					message: '<pre class="my-0"><code>' + JSON.stringify(exception, null, 4) + '</code></pre>',
					closeButton: false
				});
			}
		});

		console.error(exception);
	}

	SaveUserSetting(settingsKey, value)
	{
		this.Grocy.UserSettings[settingsKey] = value;

		var jsonData = {};
		jsonData.value = value;
		this.Api.Put('user/settings/' + settingsKey, jsonData,
			function(result)
			{
				// Nothing to do...
			},
			function(xhr)
			{
				if (!xhr.statusText.isEmpty())
				{
					this.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			}
		);
	}
	DeleteUserSetting(settingsKey, reloadPageOnSuccess = false)
	{
		delete this.Grocy.UserSettings[settingsKey];

		this.Delete('user/settings/' + settingsKey, {},
			function(result)
			{
				if (reloadPageOnSuccess)
				{
					window.location.reload();
				}
			},
			function(xhr)
			{
				if (!xhr.statusText.isEmpty())
				{
					this.ShowGenericError('Error while deleting, please retry', xhr.response)
				}
			}
		);
	}

	RunWebhook(webhook, data, repetitions = 1)
	{
		Object.assign(data, webhook.extra_data);
		var hasAlreadyFailed = false;

		for (var i = 0; i < repetitions; i++)
		{
			$.post(webhook.hook, data).fail(function(req, status, errorThrown)
			{
				if (!hasAlreadyFailed)
				{
					hasAlreadyFailed = true;
					this.ShowGenericError(this.Grocy.translate("Error while executing WebHook", { "status": status, "errorThrown": errorThrown }));
				}
			});
		}
	}
}

export { GrocyFrontendHelpers };