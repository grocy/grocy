function EmptyElementWhenMatches(selector, text)
{
	if ($(selector).text() === text)
	{
		$(selector).text('');
	}
}

function GetUriParam(key)
{
	var currentUri = window.location.search.substring(1);
	var vars = currentUri.split('&');

	for (var i = 0; i < vars.length; i++)
	{
		var currentParam = vars[i].split('=');

		if (currentParam[0] === key)
		{
			return currentParam[1] === undefined ? true : decodeURIComponent(currentParam[1]);
		}
	}
}

function UpdateUriParam(key, value)
{
	var queryParameters = new URLSearchParams(window.location.search);
	queryParameters.set(key, value);
	window.history.replaceState({}, "", decodeURIComponent(`${window.location.pathname}?${queryParameters}`));
}

function RemoveUriParam(key)
{
	var queryParameters = new URLSearchParams(window.location.search);
	queryParameters.delete(key);
	window.history.replaceState({}, "", decodeURIComponent(`${window.location.pathname}?${queryParameters}`));
}


function BoolVal(test)
{
	if (!test)
	{
		return false;
	}

	var anything = test.toString().toLowerCase();
	if (anything === true || anything === "true" || anything === "1" || anything === "on")
	{
		return true;
	}
	else
	{
		return false;
	}
}

function GetFileNameFromPath(path)
{
	return path.split("/").pop().split("\\").pop();
}

$.extend($.expr[":"],
	{
		"contains_case_insensitive": function(elem, i, match, array)
		{
			return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
		}
	});


$.fn.hasAttr = function(name)
{
	return this.attr(name) !== undefined;
};

function IsJsonString(text)
{
	try
	{
		JSON.parse(text);
	} catch (e)
	{
		return false;
	}
	return true;
}

function Delay(callable, delayMilliseconds)
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

$.fn.isVisibleInViewport = function(extraHeightPadding = 0)
{
	var elementTop = $(this).offset().top;
	var viewportTop = $(window).scrollTop() - extraHeightPadding;

	return elementTop + $(this).outerHeight() > viewportTop && elementTop < viewportTop + $(window).height();
};

function animateCSS(selector, animationName, callback, speed = "faster")
{
	var nodes = $(selector);
	nodes.addClass('animated').addClass(speed).addClass(animationName);

	function handleAnimationEnd()
	{
		nodes.removeClass('animated').removeClass(speed).removeClass(animationName);
		nodes.unbind('animationend', handleAnimationEnd);

		if (typeof callback === 'function')
		{
			callback();
		}
	}

	nodes.on('animationend', handleAnimationEnd);
}

function RandomString()
{
	return Math.random().toString(36).substring(2, 100) + Math.random().toString(36).substring(2, 100);
}

export
{
	RandomString,
	animateCSS,
	Delay,
	IsJsonString,
	BoolVal,
	GetFileNameFromPath,
	RemoveUriParam,
	UpdateUriParam,
	GetUriParam,
	EmptyElementWhenMatches
}