$(document).on("click", ".print-all-locations-button", function(e)
{
	$(".page").removeClass("d-print-none").removeClass("no-page-break");
	$(".print-timestamp").text(moment().format("l LT"));
	window.print();
});

$(document).on("click", ".print-single-location-button", function(e)
{
	$(".page").addClass("d-print-none");
	$(e.currentTarget).closest(".page").removeClass("d-print-none").addClass("no-page-break");
	$(".print-timestamp").text(moment().format("l LT"));
	window.print();
});

$("#include-out-of-stock").change(function()
{
	if (this.checked)
	{
		RemoveUriParam("include_out_of_stock");
	}
	else
	{
		UpdateUriParam("include_out_of_stock", true);
	}

	window.location.reload();
});

