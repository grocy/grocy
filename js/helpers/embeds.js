function ResizeResponsiveEmbeds(fillEntireViewport = false)
{
	var maxHeight = null;
	if (!fillEntireViewport)
	{
		maxHeight = $("body").height() - $("#mainNav").outerHeight() - 62;
	}
	else
	{
		maxHeight = $("body").height();
	}

	$("embed.embed-responsive").attr("height", maxHeight.toString() + "px");

	$("iframe.embed-responsive").each(function()
	{
		$(this).attr("height", $(this)[0].contentWindow.document.body.scrollHeight.toString() + "px");
	});
}

export { ResizeResponsiveEmbeds }