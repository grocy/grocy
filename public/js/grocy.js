L = function(text, ...placeholderValues)
{
	var localizedText = Grocy.LocalizationStrings[text];
	if (localizedText === undefined)
	{
		localizedText = text;
	}
	
	for (var i = 0; i < placeholderValues.length; i++)
	{
		localizedText = localizedText.replace('#' + (i + 1), placeholderValues[i]);
	}
	
	return localizedText;
}

U = function(relativePath)
{
	return Grocy.BaseUrl.replace(/\/$/, '') + relativePath;
}

Pluralize = function(number, singularForm, pluralForm)
{
	var text = singularForm;
	if (number != 1 && pluralForm !== null && !pluralForm.isEmpty())
	{
		text = pluralForm;
	}
	return text;
}

if (!Grocy.ActiveNav.isEmpty())
{
	var menuItem = $('#sidebarResponsive').find("[data-nav-for-page='" + Grocy.ActiveNav + "']");
	menuItem.addClass('active-page');

	var parentMenuSelector = menuItem.data("sub-menu-of");
	if (typeof parentMenuSelector !== "undefined")
	{
		$(parentMenuSelector).collapse("show");
		$(parentMenuSelector).prev(".nav-link-collapse").addClass("active-page");
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
RefreshContextualTimeago = function()
{	
	$("time.timeago").each(function()
	{
		var element = $(this);
		var timestamp = element.attr("datetime");
		element.timeago("update", timestamp);
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

Grocy.Api = { };
Grocy.Api.Get = function(apiFunction, success, error)
{
	var xhr = new XMLHttpRequest();
	var url = U('/api/' + apiFunction);

	xhr.onreadystatechange = function()
	{
		if (xhr.readyState === XMLHttpRequest.DONE)
		{
			if (xhr.status === 200)
			{
				if (success)
				{
					success(JSON.parse(xhr.responseText));
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
			if (xhr.status === 200)
			{
				if (success)
				{
					success(JSON.parse(xhr.responseText));
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

Grocy.FrontendHelpers = { };
Grocy.FrontendHelpers.ValidateForm = function(formId)
{
	var form = document.getElementById(formId);
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

Grocy.FrontendHelpers.ShowGenericError = function(message, exception)
{
	toastr.error(L(message) + '<br><br>' + L('Click to show technical details'), '', {
		onclick: function()
		{
			bootbox.alert({
				title: L('Error details'),
				message: JSON.stringify(exception, null, 4)
			});
		}
	});
	
	console.error(exception);
}

$("form").on("keyup paste", "input, textarea", function()
{
	$(this).closest("form").addClass("is-dirty");
});
$("form").on("click", "select", function()
{
	$(this).closest("form").addClass("is-dirty");
});
