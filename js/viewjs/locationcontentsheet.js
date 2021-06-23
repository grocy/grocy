function locationcontentsheetView(Grocy, scope = null)
{
	var $scope = $;
	var top = scope != null ? $(scope) : $(document);
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	top.on("click", ".print-all-locations-button", function(e)
	{
		$scope(".page").removeClass("d-print-none").removeClass("no-page-break");
		$scope(".print-timestamp").text(moment().format("l LT"));
		window.print();
	});

	top.on("click", ".print-single-location-button", function(e)
	{
		$scope(".page").addClass("d-print-none");
		$scope(e.currentTarget).closest(".page").removeClass("d-print-none").addClass("no-page-break");
		$scope(".print-timestamp").text(moment().format("l LT"));
		window.print();
	});

}
