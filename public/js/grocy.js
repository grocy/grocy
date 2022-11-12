Grocy.Api = {};
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
						success({});
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
						success({});
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
						success({});
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
						success({});
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
						success({});
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
						success({});
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

U = function(relativePath)
{
	return Grocy.BaseUrl.replace(/\/$/, '') + relativePath;
}

Grocy.Translator = new Translator(Grocy.LocalizationStrings);
Grocy.TranslatorQu = new Translator(Grocy.LocalizationStringsQu);
__t = function(text, ...placeholderValues)
{
	if (Grocy.Mode === "dev")
	{
		var text2 = text;
		if (Grocy.LocalizationStrings && !Grocy.LocalizationStrings.messages[""].hasOwnProperty(text2))
		{
			Grocy.Api.Post('system/log-missing-localization', { "text": text2 });
		}
	}

	return Grocy.Translator.__(text, ...placeholderValues)
}
__n = function(number, singularForm, pluralForm, isQu = false)
{
	if (Grocy.Mode === "dev")
	{
		var singularForm2 = singularForm;
		if (Grocy.LocalizationStrings && !Grocy.LocalizationStrings.messages[""].hasOwnProperty(singularForm2))
		{
			Grocy.Api.Post('system/log-missing-localization', { "text": singularForm2 });
		}
	}

	if (pluralForm == null || pluralForm.isEmpty())
	{
		pluralForm = singularForm;
	}

	if (isQu)
	{
		return Grocy.TranslatorQu.n__(singularForm, pluralForm, Math.abs(number), Math.abs(number))
	}
	else
	{
		return Grocy.Translator.n__(singularForm, pluralForm, Math.abs(number), Math.abs(number))
	}
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

			$(parentMenuSelector).on("shown.bs.collapse", function(e)
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

		if (timestamp.isEmpty() || timestamp.length < 10)
		{
			element.text("")
			return
		}

		if (!moment(timestamp).isValid())
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
			element.text(moment(timestamp).fromNow());
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

Grocy.FrontendHelpers = {};
Grocy.FrontendHelpers.ValidateForm = function(formId, reportValidity = false)
{
	var form = document.getElementById(formId);
	if (form === null || form === undefined)
	{
		return;
	}

	$(form).addClass('was-validated');

	if (reportValidity)
	{
		form.reportValidity();
	}

	return form.checkValidity();
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
			var errorDetails = JSON.stringify(exception, null, 4);
			if (typeof exception === "object" && exception !== null && exception.hasOwnProperty("error_message"))
			{
				errorDetails = exception.error_message;
			}

			bootbox.alert({
				title: __t('Error details'),
				message: '<p class="text-monospace my-0">' + errorDetails + '</p>',
				closeButton: false
			});
		}
	});

	console.error(exception);
}

Grocy.FrontendHelpers.SaveUserSetting = function(settingsKey, value, force = false)
{
	if (Grocy.UserSettings[settingsKey] == value && !force)
	{
		return;
	}

	Grocy.UserSettings[settingsKey] = value;

	jsonData = {};
	jsonData.value = value;
	Grocy.Api.Put('user/settings/' + settingsKey, jsonData,
		function(result)
		{
			// Nothing to do...
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

Grocy.FrontendHelpers.DeleteUserSetting = function(settingsKey, reloadPageOnSuccess = false)
{
	delete Grocy.UserSettings[settingsKey];

	Grocy.Api.Delete('user/settings/' + settingsKey, {},
		function(result)
		{
			if (reloadPageOnSuccess)
			{
				location.reload();
			}
		},
		function(xhr)
		{
			if (!xhr.statusText.isEmpty())
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while deleting, please retry', xhr.response)
			}
		}
	);
}

Grocy.FrontendHelpers.RunWebhook = function(webhook, data, repetitions = 1)
{
	Object.assign(data, webhook.extra_data);
	var hasAlreadyFailed = false;

	for (i = 0; i < repetitions; i++)
	{
		if (webhook.json)
		{
			$.ajax(webhook.hook, { "data": JSON.stringify(data), "contentType": "application/json", "type": "POST" }).fail(function(req, status, errorThrown)
			{
				if (!hasAlreadyFailed)
				{
					hasAlreadyFailed = true;
					Grocy.FrontendHelpers.ShowGenericError(__t("Error while executing WebHook", { "status": status, "errorThrown": errorThrown }));
				}
			});
		}
		else
		{
			$.post(webhook.hook, data).fail(function(req, status, errorThrown)
			{
				if (!hasAlreadyFailed)
				{
					hasAlreadyFailed = true;
					Grocy.FrontendHelpers.ShowGenericError(__t("Error while executing WebHook", { "status": status, "errorThrown": errorThrown }));
				}
			});
		}
	}
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

	if (!element[0].checkValidity())
	{
		return;
	}

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

	Grocy.FrontendHelpers.SaveUserSetting(settingKey, value);
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
	var obj = {};
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
		var text = $(this).text();
		if (isNaN(text) || text.isEmpty())
		{
			return;
		}

		var value = parseFloat(text);
		$(this).text(value.toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }));
	});

	$(rootSelector + " .locale-number.locale-number-quantity-amount").each(function()
	{
		var text = $(this).text();
		if (isNaN(text) || text.isEmpty())
		{
			return;
		}

		var value = parseFloat(text);
		$(this).text(value.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }));
	});

	$(rootSelector + " .locale-number.locale-number-generic").each(function()
	{
		var text = $(this).text();
		if (isNaN(text) || text.isEmpty())
		{
			return;
		}

		var value = parseFloat(text);
		$(this).text(value.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }));
	});
}
RefreshLocaleNumberDisplay();

function RefreshLocaleNumberInput(rootSelector = "#page-content")
{
	$(rootSelector + " .locale-number-input.locale-number-currency").each(function()
	{
		var value = $(this).val();
		if (isNaN(value) || value.toString().isEmpty())
		{
			return;
		}

		$(this).val(parseFloat(value).toLocaleString("en", { minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_input, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_input, useGrouping: false }));
	});

	$(rootSelector + " .locale-number-input.locale-number-quantity-amount").each(function()
	{
		var value = $(this).val();
		if (isNaN(value) || value.toString().isEmpty())
		{
			return;
		}

		$(this).val(parseFloat(value).toLocaleString("en", { minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts, useGrouping: false }));
	});

	$(rootSelector + " .locale-number-input.locale-number-generic").each(function()
	{
		var value = $(this).val();
		if (isNaN(value) || value.toString().isEmpty())
		{
			return;
		}

		$(this).val(value.toLocaleString("en", { minimumFractionDigits: 0, maximumFractionDigits: 2, useGrouping: false }));
	});
}
RefreshLocaleNumberInput();

$(document).on("click", ".easy-link-copy-textbox", function()
{
	$(this).select();
});

$("textarea.wysiwyg-editor").summernote({
	minHeight: "300px",
	lang: __t("summernote_locale"),
	callbacks: {
		onImageLinkInsert: function(url)
		{
			// Summernote workaround: Make images responsive
			// By adding the "img-fluid" class to the img tag
			$img = $('<img>').attr({ src: url, class: "img-fluid" })
			$(this).summernote("insertNode", $img[0]);
		}
	}
});

// Summernote workaround: Make embeds responsive
// By wrapping any embeded video in a container with class "embed-responsive"
$(".note-video-clip").each(function()
{
	$(this).parent().html('<div class="embed-responsive embed-responsive-16by9">' + $(this).wrap("<p/>").parent().html() + "</div>");
});

function LoadImagesLazy()
{
	$(".lazy:visible").Lazy({
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
				label: __t('Close'),
				className: 'btn-secondary responsive-button',
				callback: function()
				{
					bootbox.hideAll();
				}
			}
		}
	});
});

// Default DataTables initialisation settings
var collapsedGroups = {};
$.extend(true, $.fn.dataTable.defaults, {
	'paginate': false,
	'deferRender': true,
	'language': IsJsonString(__t('datatables_localization')) ? JSON.parse(__t('datatables_localization')) : {},
	'scrollY': false,
	'scrollX': true,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	},
	'stateSaveCallback': function(settings, data)
	{
		var settingKey = 'datatables_state_' + settings.sTableId;
		if ($.isEmptyObject(data))
		{
			//state.clear was called and unfortunately the table is not refresh, so we are reloading the page
			Grocy.FrontendHelpers.DeleteUserSetting(settingKey, true);
		} else
		{
			var stateData = JSON.stringify(data);
			Grocy.FrontendHelpers.SaveUserSetting(settingKey, stateData);
		}
	},
	'stateLoadCallback': function(settings, data)
	{
		var settingKey = 'datatables_state_' + settings.sTableId;

		if (Grocy.UserSettings[settingKey] == undefined)
		{
			return null;
		}
		else
		{
			return JSON.parse(Grocy.UserSettings[settingKey]);
		}
	},
	'preDrawCallback': function(settings)
	{
		// Currently it is not possible to save the state of rowGroup via saveState events
		var api = new $.fn.dataTable.Api(settings);
		if (typeof api.rowGroup === "function")
		{
			var settingKey = 'datatables_rowGroup_' + settings.sTableId;
			if (Grocy.UserSettings[settingKey] !== undefined)
			{
				var rowGroup = JSON.parse(Grocy.UserSettings[settingKey]);

				// Check if there way changed. the draw event is called often therefore we have to check if it's really necessary
				if (rowGroup.enable !== api.rowGroup().enabled()
					|| ("dataSrc" in rowGroup && rowGroup.dataSrc !== api.rowGroup().dataSrc()))
				{

					api.rowGroup().enable(rowGroup.enable);

					if ("dataSrc" in rowGroup)
					{
						api.rowGroup().dataSrc(rowGroup.dataSrc);

						// Apply fixed order for group column
						var fixedOrder = {
							pre: [rowGroup.dataSrc, 'asc']
						};

						api.order.fixed(fixedOrder);
					}
				}
			}
		}
	},
	'columnDefs': [
		{ type: 'chinese-string', targets: '_all' }
	],
	'rowGroup': {
		enable: false,
		startRender: function(rows, group)
		{
			var collapsed = !!collapsedGroups[group];
			var toggleClass = collapsed ? "fa-caret-right" : "fa-caret-down";

			rows.nodes().each(function(row)
			{
				row.style.display = collapsed ? "none" : "";
			});

			return $("<tr/>")
				.append('<td colspan="' + rows.columns()[0].length + '">' + group + ' <span class="fa fa-fw d-print-none ' + toggleClass + '"/></td>')
				.attr("data-name", group)
				.toggleClass("collapsed", collapsed);
		}
	}
});
$(document).on("click", "tr.dtrg-group", function()
{
	var name = $(this).data('name');
	collapsedGroups[name] = !collapsedGroups[name];
	$("table").DataTable().draw();
});
$.fn.dataTable.ext.type.order["custom-sort-pre"] = function(data)
{
	// Workaround for https://github.com/DataTables/ColReorder/issues/85
	//
	// Custom sorting can normally be provided by a "data-order" attribute on the <td> element,
	// however this causes issues when reordering such a column...
	//
	// This here is for a custom column type "custom-sort",
	// the custom order value needs to be provided in the first child (<span>) of the <td>

	return (parseFloat($(data).get(0).innerText));
};

// serializeJSON defaults
$.serializeJSON.defaultOptions.checkboxUncheckedValue = "0";

$(Grocy.UserPermissions).each(function(index, item)
{
	if (item.has_permission == 0)
	{
		$('.permission-' + item.permission_name).addClass('disabled').addClass('not-allowed');
	}
});
$('a.link-return').not(".btn").each(function()
{
	var base = $(this).data('href');
	if (base.contains('?'))
	{
		$(this).attr('href', base + '&returnto' + encodeURIComponent(Grocy.CurrentUrlRelative));
	}
	else
	{
		$(this).attr('href', base + '?returnto=' + encodeURIComponent(Grocy.CurrentUrlRelative));
	}

})

$(document).on("click", "a.btn.link-return", function(e)
{
	e.preventDefault();

	var link = GetUriParam("returnto");
	if (!link || !link.length > 0)
	{
		location.href = $(e.currentTarget).attr("href");
	}
	else
	{
		location.href = U(link);
	}
});

$('.dropdown-item').has('.form-check input[type=checkbox]').on('click', function(e)
{
	if ($(e.target).is('div.form-check') || $(e.target).is('div.dropdown-item'))
	{
		$(e.target).find('input[type=checkbox]').click();
	}
})

$('.table').on('column-sizing.dt', function(e, settings)
{
	var dtScrollWidth = $('.dataTables_scroll').width();
	var tableWidth = $('.table').width() + 100; // Some extra padding, otherwise the scrollbar maybe only appears after a column is already completely out of the viewport

	if (dtScrollWidth < tableWidth)
	{
		$('.dataTables_scrollBody').addClass("no-force-overflow-visible");
		$('.dataTables_scrollBody').removeClass("force-overflow-visible");
	}
	else
	{
		$('.dataTables_scrollBody').removeClass("no-force-overflow-visible");
		$('.dataTables_scrollBody').addClass("force-overflow-visible");
	}
});
$('td .dropdown').on('show.bs.dropdown', function(e)
{
	if ($('.dataTables_scrollBody').hasClass("no-force-overflow-visible"))
	{
		$('.dataTables_scrollBody').addClass("force-overflow-visible");
	}
});
$("td .dropdown").on('hide.bs.dropdown', function(e)
{
	if ($('.dataTables_scrollBody').hasClass("no-force-overflow-visible"))
	{
		$('.dataTables_scrollBody').removeClass("force-overflow-visible");
	}
})

$(window).on("message", function(e)
{
	var data = e.originalEvent.data;

	if (data.Message === "Reload")
	{
		window.location.reload();
	}
});

$(".change-table-columns-visibility-button").on("click", function(e)
{
	e.preventDefault();

	var dataTableSelector = $(e.currentTarget).attr("data-table-selector");
	var dataTable = $(dataTableSelector).DataTable();

	var columnCheckBoxesHtml = "";
	var rowGroupRadioBoxesHtml = "";

	var rowGroupDefined = typeof dataTable.rowGroup === "function";

	if (rowGroupDefined)
	{
		var rowGroupChecked = (dataTable.rowGroup().enabled()) ? "" : "checked";
		rowGroupRadioBoxesHtml = ' \
			<div class="custom-control custom-radio custom-control-inline"> \
				<input ' + rowGroupChecked + ' class="custom-control-input change-table-columns-rowgroup-toggle" \
					type="radio" \
					name="column-rowgroup" \
					id="column-rowgroup-none" \
					data-table-selector="' + dataTableSelector + '" \
					data-column-index="-1" \
				> \
				<label class="custom-control-label font-italic" \
					for="column-rowgroup-none">' + __t("None") + ' \
				</label > \
			</div>';
	}

	dataTable.columns().every(function()
	{
		var index = this.index();
		var headerCell = $(this.header());
		var title = headerCell.text();
		var visible = this.visible();

		if (title.isEmpty() || title.startsWith("Hidden") || headerCell.hasClass("d-none"))
		{
			return;
		}

		var shadowColumnIndex = headerCell.attr("data-shadow-rowgroup-column");
		if (shadowColumnIndex)
		{
			index = shadowColumnIndex;
		}

		var checked = "checked";
		if (!visible)
		{
			checked = "";
		}

		columnCheckBoxesHtml += ' \
			<div class="custom-control custom-checkbox"> \
				<input ' + checked + ' class="form-check-input custom-control-input change-table-columns-visibility-toggle" \
					type="checkbox" \
					id="column-' + index.toString() + '" \
					data-table-selector="' + dataTableSelector + '" \
					data-column-index="' + index.toString() + '" \
					value="1"> \
				<label class="form-check-label custom-control-label" \
					for="column-' + index.toString() + '">' + title + ' \
				</label> \
			</div>';

		if (rowGroupDefined && headerCell.hasClass("allow-grouping"))
		{
			var rowGroupChecked = "";
			if (dataTable.rowGroup().enabled() && dataTable.rowGroup().dataSrc() == index)
			{
				rowGroupChecked = "checked";
			}

			rowGroupRadioBoxesHtml += ' \
			<div class="custom-control custom-radio"> \
				<input ' + rowGroupChecked + ' class="custom-control-input change-table-columns-rowgroup-toggle" \
					type="radio" \
					name="column-rowgroup" \
					id="column-rowgroup-' + index.toString() + '" \
					data-table-selector="' + dataTableSelector + '" \
					data-column-index="' + index.toString() + '" \
				> \
				<label class="custom-control-label" \
					for="column-rowgroup-' + index.toString() + '">' + title + ' \
				</label > \
			</div>';
		}
	});

	var message = '\
		<div class="text-center"> \
			<h5>' + __t('Table options') + '</h5> \
			<hr> \
			<h5 class="mb-0">' + __t('Hide/view columns') + '</h5> \
			<div class="text-left form-group"> \
				' + columnCheckBoxesHtml + ' \
			</div> \
		</div>';

	if (rowGroupDefined)
	{
		message += ' \
			<div class="text-center mt-1"> \
				<h5 class="pt-3 mb-0">' + __t('Group by') + '</h5> \
				<div class="text-left form-group"> \
					' + rowGroupRadioBoxesHtml + ' \
				</div> \
			</div>';
	}

	bootbox.dialog({
		message: message,
		size: 'small',
		backdrop: true,
		closeButton: false,
		buttons: {
			reset: {
				label: __t('Reset'),
				className: 'btn-outline-danger float-left responsive-button',
				callback: function()
				{
					bootbox.confirm({
						message: __t("Are you sure to reset the table options?"),
						buttons: {
							cancel: {
								label: 'No',
								className: 'btn-danger'
							},
							confirm: {
								label: 'Yes',
								className: 'btn-success'
							}
						},
						callback: function(result)
						{
							if (result)
							{
								var dataTable = $(dataTableSelector).DataTable();
								var tableId = dataTable.settings()[0].sTableId;

								// Delete rowgroup settings
								Grocy.FrontendHelpers.DeleteUserSetting('datatables_rowGroup_' + tableId);

								// Delete state settings
								dataTable.state.clear();
							}
							bootbox.hideAll();
						}
					});
				}
			},
			ok: {
				label: __t('OK'),
				className: 'btn-primary responsive-button',
				callback: function()
				{
					bootbox.hideAll();
				}
			}
		}
	});
});

$(document).on("click", ".change-table-columns-visibility-toggle", function()
{
	var dataTableSelector = $(this).attr("data-table-selector");
	var columnIndex = $(this).attr("data-column-index");
	var dataTable = $(dataTableSelector).DataTable();

	dataTable.columns(columnIndex).visible(this.checked);
	LoadImagesLazy();
});


$(document).on("click", ".change-table-columns-rowgroup-toggle", function()
{
	var dataTableSelector = $(this).attr("data-table-selector");
	var columnIndex = $(this).attr("data-column-index");
	var dataTable = $(dataTableSelector).DataTable();
	var rowGroup;

	if (columnIndex == -1)
	{
		rowGroup = {
			enable: false
		};

		dataTable.rowGroup().enable(false);

		// Remove fixed order
		dataTable.order.fixed({});
	}
	else
	{
		rowGroup = {
			enable: true,
			dataSrc: columnIndex
		}

		dataTable.rowGroup().enable(true);
		dataTable.rowGroup().dataSrc(columnIndex);

		// Apply fixed order for group column
		var fixedOrder = {
			pre: [columnIndex, 'asc']
		};
		dataTable.order.fixed(fixedOrder);
	}

	var settingKey = 'datatables_rowGroup_' + dataTable.settings()[0].sTableId;
	Grocy.FrontendHelpers.SaveUserSetting(settingKey, JSON.stringify(rowGroup));

	dataTable.draw();
});

if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_RECIPES)
{
	if ($(window).width() < 768)
	{
		$("#meal-plan-nav-link").attr("href", $("#meal-plan-nav-link").attr("href") + "?start=" + moment().format("YYYY-MM-DD") + "&days=0");
	}
	else
	{
		$("#meal-plan-nav-link").attr("href", $("#meal-plan-nav-link").attr("href") + "?start=" + moment().startOf("week").format("YYYY-MM-DD"));
	}
}

$('[data-toggle="tooltip"][data-html="true"]').on("shown.bs.tooltip", function()
{
	RefreshLocaleNumberDisplay(".tooltip");
})

$(document).on("click", "#clear-filter-button", function(e)
{
	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();
});
