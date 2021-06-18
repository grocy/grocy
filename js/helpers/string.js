
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