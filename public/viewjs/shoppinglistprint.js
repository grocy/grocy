$(function()
{
	$(".print-timestamp").text(moment().format("l LT"));

	const url = new URL(window.location);
	//open print dialog only if the parameter "preview" is not set
	if (!url.searchParams.has("preview"))
	{
		window.print();

		//redirect to the shoppinglist
		url.searchParams.delete("print");
		window.location.replace(url);
	}
});