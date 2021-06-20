
class GrocyFrontendHelpers
{
	constructor(Grocy, Api)
	{
		this.Grocy = Grocy;
		this.Api = Api;
	}

	Delay(callable, delayMilliseconds)
	{
		var timer = 0;
		return function()
		{
			var context = this;
			var args = arguments;

			clearTimeout(timer);
			timer = setTimeout(function()
			{
				callable.apply(context, args);
			}, delayMilliseconds || 0);
		};
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

	InitDataTable(dataTable, searchFunction = null, clearFunction = null)
	{
		dataTable.columns.adjust().draw();

		var self = this;

		var defaultSearchFunction = function()
		{
			var value = $(this).val();
			if (value === "all")
			{
				value = "";
			}

			dataTable.search(value).draw();
		};

		var defaultClearFunction = function()
		{
			$("#search").val("");
			dataTable.search("").draw();
		};

		$("#search").on("keyup", self.Delay(searchFunction || defaultSearchFunction, 200));

		$("#clear-filter-button").on("click", clearFunction || defaultClearFunction);
	}

	MakeFilterForColumn(selector, column, table, filterFunction = null, transferCss = false, valueMod = null)
	{
		$(selector).on("change", filterFunction || function()
		{
			var value = $(this).val();
			var text = $(selector + " option:selected").text();
			if (value === "all")
			{
				text = "";
			}
			else
			{
				value = valueMod != null ? valueMod(value) : value;
			}

			if (transferCss)
			{
				// Transfer CSS classes of selected element to dropdown element (for background)
				$(this).attr("class", $("#" + $(this).attr("id") + " option[value='" + value + "']").attr("class") + " form-control");
			}

			table.column(column).search(text).draw();
		});
		$("#clear-filter-button").on('click', () =>
		{
			$(selector).val("");
			table.column(column).search("").draw();
		})
	}
	MakeStatusFilter(dataTable, column)
	{
		$("#status-filter").on("change", function()
		{
			var value = $(this).val();
			if (value === "all")
			{
				value = "";
			}

			// Transfer CSS classes of selected element to dropdown element (for background)
			$(this).attr("class", $("#" + $(this).attr("id") + " option[value='" + value + "']").attr("class") + " form-control");

			dataTable.column(column).search(value).draw();
		});

		$(".status-filter-message").on("click", function()
		{
			var value = $(this).data("status-filter");
			$("#status-filter").val(value);
			$("#status-filter").trigger("change");
		});

		$("#clear-filter-button").on("click", function()
		{
			$("#status-filter").val("all");
			$("#status-filter").trigger("change");
		});
	}
}

export { GrocyFrontendHelpers };