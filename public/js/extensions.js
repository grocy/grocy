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

GetUriParam = function(key)
{
	var currentUri = decodeURIComponent(window.location.search.substring(1));
	var vars = currentUri.split('&');

	for (i = 0; i < vars.length; i++)
	{
		var currentParam = vars[i].split('=');

		if (currentParam[0] === key)
		{
			return currentParam[1] === undefined ? true : currentParam[1];
		}
	}
};
