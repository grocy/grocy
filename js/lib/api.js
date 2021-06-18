
class GrocyApi
{
	constructor(Grocy)
	{
		this.Grocy = Grocy;
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