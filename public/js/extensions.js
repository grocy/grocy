EmptyElementWhenMatches = function(selector, text)
{
	if ($(selector).text() === text)
	{
		$(selector).text('');
	}
};

String.prototype.contains = function(search)
{
	return this.toLowerCase().indexOf(search.toLowerCase()) !== -1;
};

String.prototype.isEmpty = function()
{
	return (this.length === 0 || !this.trim());
};

String.prototype.replaceAll = function(search, replacement)
{
	return this.replace(new RegExp(search, "g"), replacement);
};

GetUriParam = function(key)
{
	var currentUri = window.location.search.substring(1);
	var vars = currentUri.split('&');

	for (i = 0; i < vars.length; i++)
	{
		var currentParam = vars[i].split('=');

		if (currentParam[0] === key)
		{
			return currentParam[1] === undefined ? true : decodeURIComponent(currentParam[1]);
		}
	}
};

UpdateUriParam = function(key, value)
{
	var queryParameters = new URLSearchParams(location.search);
	queryParameters.set(key, value);
	window.history.replaceState({ }, "", decodeURIComponent(`${location.pathname}?${queryParameters}`));
};

IsTouchInputDevice = function()
{
	if (("ontouchstart" in window) || window.DocumentTouch && document instanceof DocumentTouch)
	{
		return true;
	}

	return false;
}

BoolVal = function(test)
{
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

GetFileNameFromPath = function(path)
{
	return path.split("/").pop().split("\\").pop();
}

GetFileExtension = function(pathOrFileName)
{
	return pathOrFileName.split(".").pop();
}

$.extend($.expr[":"],
{
	"contains_case_insensitive": function(elem, i, match, array)
	{
		return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
	}
});

FindObjectInArrayByPropertyValue = function(array, propertyName, propertyValue)
{
	for (var i = 0; i < array.length; i++)
	{
		if (array[i][propertyName] == propertyValue)
		{
            return array[i];
        }
	}
	
    return null;
}

FindAllObjectsInArrayByPropertyValue = function(array, propertyName, propertyValue)
{
	var returnArray = [];
	
	for (var i = 0; i < array.length; i++)
	{
		if (array[i][propertyName] == propertyValue)
		{
			returnArray.push(array[i]);
        }
	}
	
	return returnArray;
}

$.fn.hasAttr = function(name)
{
	return this.attr(name) !== undefined;
};

function IsJsonString(text)
{
	try
	{
		JSON.parse(text);
	} catch(e)
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
