Grocy.Api = { };
Grocy.Api.Get = function(apiFunction, success, error)
{
	var xhr = new XMLHttpRequest();
	var url = U('/api/' + apiFunction);

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState === XMLHttpRequest.DONE)
		{
			if (xhr.status === 200 || xhr.status === 204)
			{
				if (success)
				{
					if (xhr.status === 200)
					{
						success(JSON.parse(xhr.responseText));
					}
					else
					{
						success({ });
					}
				}
			}
			else
			{
				if (error)
				{
					error(xhr);
				}
			}
		}
	};

	xhr.open('GET', url, true);
	xhr.send();
};

Grocy.Api.Post = function(apiFunction, jsonData, success, error)
{
	var xhr = new XMLHttpRequest();
	var url = U('/api/' + apiFunction);

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState === XMLHttpRequest.DONE)
		{
			if (xhr.status === 200 || xhr.status === 204)
			{
				if (success)
				{
					if (xhr.status === 200)
					{
						success(JSON.parse(xhr.responseText));
					}
					else
					{
						success({ });
					}
				}
			}
			else
			{
				if (error)
				{
					error(xhr);
				}
			}
		}
	};

	xhr.open('POST', url, true);
	xhr.setRequestHeader('Content-type', 'application/json');
	xhr.send(JSON.stringify(jsonData));
};

Grocy.Api.Put = function(apiFunction, jsonData, success, error)
{
	var xhr = new XMLHttpRequest();
	var url = U('/api/' + apiFunction);

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState === XMLHttpRequest.DONE)
		{
			if (xhr.status === 200 || xhr.status === 204)
			{
				if (success)
				{
					if (xhr.status === 200)
					{
						success(JSON.parse(xhr.responseText));
					}
					else
					{
						success({ });
					}
				}
			}
			else
			{
				if (error)
				{
					error(xhr);
				}
			}
		}
	};

	xhr.open('PUT', url, true);
	xhr.setRequestHeader('Content-type', 'application/json');
	xhr.send(JSON.stringify(jsonData));
};

Grocy.Api.Delete = function(apiFunction, jsonData, success, error)
{
	var xhr = new XMLHttpRequest();
	var url = U('/api/' + apiFunction);

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState === XMLHttpRequest.DONE)
		{
			if (xhr.status === 200 || xhr.status === 204)
			{
				if (success)
				{
					if (xhr.status === 200)
					{
						success(JSON.parse(xhr.responseText));
					}
					else
					{
						success({ });
					}
				}
			}
			else
			{
				if (error)
				{
					error(xhr);
				}
			}
		}
	};

	xhr.open('DELETE', url, true);
	xhr.setRequestHeader('Content-type', 'application/json');
	xhr.send(JSON.stringify(jsonData));
};

Grocy.Api.UploadFile = function(file, group, fileName, success, error)
{
	var xhr = new XMLHttpRequest();
	var url = U('/api/files/' + group + '/' + btoa(fileName));

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState === XMLHttpRequest.DONE)
		{
			if (xhr.status === 200 || xhr.status === 204)
			{
				if (success)
				{
					if (xhr.status === 200)
					{
						success(JSON.parse(xhr.responseText));
					}
					else
					{
						success({ });
					}
				}
			}
			else
			{
				if (error)
				{
					error(xhr);
				}
			}
		}
	};

	xhr.open('PUT', url, true);
	xhr.setRequestHeader('Content-type', 'application/octet-stream');
	xhr.send(file);
};

Grocy.Api.DeleteFile = function(fileName, group, success, error)
{
	var xhr = new XMLHttpRequest();
	var url = U('/api/files/' + group + '/' + btoa(fileName));

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState === XMLHttpRequest.DONE)
		{
			if (xhr.status === 200 || xhr.status === 204)
			{
				if (success)
				{
					if (xhr.status === 200)
					{
						success(JSON.parse(xhr.responseText));
					}
					else
					{
						success({ });
					}
				}
			}
			else
			{
				if (error)
				{
					error(xhr);
				}
			}
		}
	};

	xhr.open('DELETE', url, true);
	xhr.setRequestHeader('Content-type', 'application/json');
	xhr.send();
};

Grocy.Translator = new Translator(Grocy.GettextPo);
__t = function(text, ...placeholderValues)
{
	if (Grocy.Mode === "dev")
	{
		var text2 = text;
		Grocy.Api.Post('system/log-missing-localization', { "text": text2 });
	}
	
	return Grocy.Translator.__(text, ...placeholderValues)
}
__n = function(number, singularForm, pluralForm)
{
	if (Grocy.Mode === "dev")
	{
		var singularForm2 = singularForm;
		Grocy.Api.Post('system/log-missing-localization', { "text": singularForm2 });
	}

	return Grocy.Translator.n__(singularForm, pluralForm, number, number)
}

U = function(relativePath)
{
	return Grocy.BaseUrl.replace(/\/$/, '') + relativePath;
}

if (!Grocy.ActiveNav.isEmpty())
{
	var menuItem = $('#sidebarResponsive').find("[data-nav-for-page='" + Grocy.ActiveNav + "']");
	menuItem.addClass('active-page');

	if (menuItem.length)
	{
		var parentMenuSelector = menuItem.data("sub-menu-of");
		if (typeof parentMenuSelector !== "undefined")
		{
			$(parentMenuSelector).collapse("show");
			$(parentMenuSelector).prev(".nav-link-collapse").addClass("active-page");

			$(parentMenuSelector).on("shown.bs.collapse", function (e)
			{
				if (!menuItem.isVisibleInViewport(75))
				{
					menuItem[0].scrollIntoView();
				}
			})
		}
		else
		{
			if (!menuItem.isVisibleInViewport(75))
			{
				menuItem[0].scrollIntoView();
			}
		}
	}
}

var observer = new MutationObserver(function(mutations)
{
	mutations.forEach(function(mutation)
	{
		if (mutation.attributeName === "class")
		{
			var attributeValue = $(mutation.target).prop(mutation.attributeName);
			if (attributeValue.contains("sidenav-toggled"))
			{
				window.localStorage.setItem("sidebar_state", "collapsed");
			}
			else
			{
				window.localStorage.setItem("sidebar_state", "expanded");
			}
		}
	});
});
observer.observe(document.body, {
	attributes: true
});
if (window.localStorage.getItem("sidebar_state") === "collapsed")
{
	$("#sidenavToggler").click();
}

$.timeago.settings.allowFuture = true;
RefreshContextualTimeago = function(rootSelector = "#page-content")
{
	$(rootSelector + " time.timeago").each(function()
	{
		var element = $(this);

		if (!element.hasAttr("datetime"))
		{
			element.text("")
			return
		}

		var timestamp = element.attr("datetime");

		if (timestamp.isEmpty())
		{
			element.text("")
			return
		}

		var isNever = timestamp && timestamp.substring(0, 10) == "2999-12-31";
		var isToday = timestamp && timestamp.substring(0, 10) == moment().format("YYYY-MM-DD");
		var isDateWithoutTime = element.hasClass("timeago-date-only");

		if (isNever)
		{
			element.prev().text(__t("Never"));
			element.text("");
		}
		else if (isToday)
		{
			element.text(__t("Today"));
		}
		else
		{
			element.timeago("update", timestamp);
		}

		if (isDateWithoutTime)
		{
			element.prev().text(element.prev().text().substring(0, 10));
		}
	});
}
RefreshContextualTimeago();

toastr.options = {
	toastClass: 'alert',
	closeButton: true,
	timeOut: 20000,
	extendedTimeOut: 5000
};

window.FontAwesomeConfig = {
	searchPseudoElements: true
}

// Don't show tooltips on touch input devices
if (IsTouchInputDevice())
{
	var css = document.createElement("style");
	css.innerHTML = ".tooltip { display: none; }";
	document.body.appendChild(css);
}

Grocy.FrontendHelpers = { };
Grocy.FrontendHelpers.ValidateForm = function(formId)
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

Grocy.FrontendHelpers.BeginUiBusy = function(formId = null)
{
	$("body").addClass("cursor-busy");

	if (formId !== null)
	{
		$("#" + formId + " :input").attr("disabled", true);
	}
}

Grocy.FrontendHelpers.EndUiBusy = function(formId = null)
{
	$("body").removeClass("cursor-busy");

	if (formId !== null)
	{
		$("#" + formId + " :input").attr("disabled", false);
	}
}

Grocy.FrontendHelpers.ShowGenericError = function(message, exception)
{
	toastr.error(__t(message) + '<br><br>' + __t('Click to show technical details'), '', {
		onclick: function()
		{
			bootbox.alert({
				title: __t('Error details'),
				message: JSON.stringify(exception, null, 4),
				closeButton: false
			});
		}
	});

	console.error(exception);
}

$(document).on("keyup paste change", "input, textarea", function()
{
	$(this).closest("form").addClass("is-dirty");
});
$(document).on("click", "select", function()
{
	$(this).closest("form").addClass("is-dirty");
});

// Auto saving user setting controls
$(document).on("change", ".user-setting-control", function()
{
	var element = $(this);
	var settingKey = element.attr("data-setting-key");

	var inputType = "unknown";
	if (typeof element.attr("type") !== typeof undefined && element.attr("type") !== false)
	{
		inputType = element.attr("type").toLowerCase();
	}

	if (inputType === "checkbox")
	{
		value = element.is(":checked");
	}
	else
	{
		var value = element.val();
	}

	Grocy.UserSettings[settingKey] = value;

	jsonData = { };
	jsonData.value = value;
	Grocy.Api.Put('user/settings/' + settingKey, jsonData,
		function(result)
		{
			// Nothing to do...
		},
		function(xhr)
		{
			if (!xhr.statusText.isEmpty())
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		}
	);
});

// Show file name Bootstrap custom file input
$('input.custom-file-input').on('change', function()
{
	$(this).next('.custom-file-label').html(GetFileNameFromPath($(this).val()));
});

// Translation of "Browse"-button of Bootstrap custom file input
if ($(".custom-file-label").length > 0)
{
	$("<style>").html('.custom-file-label::after { content: "' + __t("Select file") + '"; }').appendTo("head");
}

ResizeResponsiveEmbeds = function(fillEntireViewport = false)
{
	if (!fillEntireViewport)
	{
		var maxHeight = $("body").height() - $("#mainNav").outerHeight() - 62;
	}
	else
	{
		var maxHeight = $("body").height();
	}

	$("embed.embed-responsive").attr("height", maxHeight.toString() + "px");

	$("iframe.embed-responsive").each(function()
	{
		$(this).attr("height", $(this)[0].contentWindow.document.body.scrollHeight.toString() + "px");
	});
}
$(window).on('resize', function()
{
	ResizeResponsiveEmbeds($("body").hasClass("fullscreen-card"));
});
$("iframe").on("load", function()
{
	ResizeResponsiveEmbeds($("body").hasClass("fullscreen-card"));
});

function WindowMessageBag(message, payload = null)
{
	var obj = { };
	obj.Message = message;
	obj.Payload = payload;
	return obj;
}

// Add border around anchor link section
if (window.location.hash)
{
	$(window.location.hash).addClass("p-2 border border-info rounded");
}

$("#about-dialog-link").on("click", function()
{
	bootbox.alert({
		message: '<iframe height="400px" class="embed-responsive" src="' + U("/about?embedded") + '"></iframe>',
		closeButton: false,
		size: "large"
	});
});

function RefreshLocaleNumberDisplay(rootSelector = "#page-content")
{
	$(rootSelector + " .locale-number.locale-number-currency").each(function()
	{
		if (isNaN(parseFloat($(this).text())))
		{
			return;
		}

		$(this).text(parseFloat($(this).text()).toLocaleString(undefined, { style: "currency", currency: Grocy.Currency }));
	});

	$(rootSelector + " .locale-number.locale-number-quantity-amount").each(function()
	{
		if (isNaN(parseFloat($(this).text())))
		{
			return;
		}

		$(this).text(parseFloat($(this).text()).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 3 }));
	});

	$(rootSelector + " .locale-number.locale-number-generic").each(function ()
	{
		if (isNaN(parseFloat($(this).text())))
		{
			return;
		}
		
		$(this).text(parseFloat($(this).text()).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 }));
	});
}
RefreshLocaleNumberDisplay();

$(document).on("click", ".easy-link-copy-textbox", function()
{
	$(this).select();
});

$("textarea.wysiwyg-editor").summernote({
	minHeight: "300px",
	lang: __t("summernote_locale")
});

function LoadImagesLazy()
{
	$(".lazy").Lazy({
		enableThrottle: true,
		throttle: 500
	});
}
LoadImagesLazy();

if (!Grocy.CalendarFirstDayOfWeek.isEmpty())
{
	moment.updateLocale(moment.locale(), {
		week: {
			dow: Grocy.CalendarFirstDayOfWeek
		}
	});
}

$(window).on("message", function(e)
{
	var data = e.originalEvent.data;

	if (data.Message === "ShowSuccessMessage")
	{
		toastr.success(data.Payload);
	}
	else if (data.Message === "CloseAllModals")
	{
		bootbox.hideAll();
	}
});

$(document).on("click", ".show-as-dialog-link", function(e)
{
	e.preventDefault();

	var link = $(e.currentTarget).attr("href");
	
	bootbox.dialog({
		message: '<iframe height="650px" class="embed-responsive" src="' + link + '"></iframe>',
		size: 'large',
		backdrop: true,
		closeButton: false,
		buttons: {
			cancel: {
				label: __t('Cancel'),
				className: 'btn-secondary responsive-button',
				callback: function ()
				{
					bootbox.hideAll();
				}
			}
		}
	});
});

// Default DataTables initialisation settings
$.extend(true, $.fn.dataTable.defaults, {
	'paginate': false,
	'deferRender': true,
	'language': IsJsonString(__t('datatables_localization')) ? JSON.parse(__t('datatables_localization')) : { },
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function (settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	}
});
