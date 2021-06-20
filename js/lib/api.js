
class GrocyApi
{
	constructor(Grocy)
	{
		this.Grocy = Grocy;
	}

	// This throws a deprecation warning in the console.
	// The "clean" solution would be to move all translations
	// To be promise-based async stuff, but Grocy not in a shape
	// to make this easily possible right now.
	// The introduction of a frontend framework like react or vue
	// will make this obsolete as well.
	LoadLanguageSync(locale)
	{
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.open("GET", this.Grocy.FormatUrl('/js/locales/grocy/' + locale + '.json'), false);
		xmlhttp.send();
		// if Status OK or NOT MODIFIED
		if ((xmlhttp.status == 200 || xmlhttp.status == 304) && xmlhttp.readyState == 4)
		{
			return JSON.parse(xmlhttp.responseText);
		}
		else
		{
			return null;
		}
	}

	Get(apiFunction, success, error)
	{
		var xhr = new XMLHttpRequest();
		var url = this.Grocy.FormatUrl('/api/' + apiFunction);

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
		}

		xhr.open('GET', url, true);
		xhr.send();
	}
	Post(apiFunction, jsonData, success, error)
	{
		var xhr = new XMLHttpRequest();
		var url = this.Grocy.FormatUrl('/api/' + apiFunction);

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
	}

	Put(apiFunction, jsonData, success, error)
	{
		var xhr = new XMLHttpRequest();
		var url = this.Grocy.FormatUrl('/api/' + apiFunction);

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
		}

		xhr.open('PUT', url, true);
		xhr.setRequestHeader('Content-type', 'application/json');
		xhr.send(JSON.stringify(jsonData));
	}
	Delete(apiFunction, jsonData, success, error)
	{
		var xhr = new XMLHttpRequest();
		var url = this.Grocy.FormatUrl('/api/' + apiFunction);

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
	}


	UploadFile(file, group, fileName, success, error)
	{
		var xhr = new XMLHttpRequest();
		var url = this.Grocy.FormatUrl('/api/files/' + group + '/' + btoa(fileName));

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
		}
		xhr.open('PUT', url, true);
		xhr.setRequestHeader('Content-type', 'application/octet-stream');
		xhr.send(file);
	}

	DeleteFile(fileName, group, success, error)
	{
		var xhr = new XMLHttpRequest();
		var url = this.Grocy.FormatUrl('/api/files/' + group + '/' + btoa(fileName));

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
	}
}

export { GrocyApi };