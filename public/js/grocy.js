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

if (!Grocy.ActiveNav.isEmpty())
{
	var menuItem = $('.nav').find("[data-nav-for-page='" + Grocy.ActiveNav + "']");
	menuItem.addClass('active');
}	

$.timeago.settings.allowFuture = true;
$('time.timeago').timeago();

Grocy.FetchJson = function(url, success, error)
{
	var xhr = new XMLHttpRequest();

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

Grocy.PostJson = function(url, jsonData, success, error)
{
	var xhr = new XMLHttpRequest();

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
