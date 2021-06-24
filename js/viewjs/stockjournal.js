function stockjournalView(Grocy, scope = null)
{
	var $scope = $;
	var top = scope != null ? $(scope) : $(document);
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	var stockJournalTable = $scope('#stock-journal-table').DataTable({
		'paginate': true,
		'order': [[3, 'desc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#stock-journal-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(stockJournalTable);

	Grocy.FrontendHelpers.MakeFilterForColumn("#product-filter", 1, stockJournalTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#transaction-type-filter", 4, stockJournalTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#location-filter", 5, stockJournalTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#user-filter", 6, stockJournalTable);

	if (typeof Grocy.GetUriParam("product") !== "undefined")
	{
		$scope("#product-filter").val(Grocy.GetUriParam("product"));
		$scope("#product-filter").trigger("change");
	}

	top.on('click', '.undo-stock-booking-button', function(e)
	{
		e.preventDefault();

		var bookingId = $scope(e.currentTarget).attr('data-booking-id');
		var correlationId = $scope("#stock-booking-" + bookingId + "-row").attr("data-correlation-id");

		var correspondingBookingsRoot = $scope("#stock-booking-" + bookingId + "-row");
		if (!correlationId.isEmpty())
		{
			correspondingBookingsRoot = $scope(".stock-booking-correlation-" + correlationId);
		}

		Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
			function(result)
			{
				correspondingBookingsRoot.addClass("text-muted");
				correspondingBookingsRoot.find("span.name-anchor").addClass("text-strike-through").after("<br>" + __t("Undone on") + " " + moment().format("YYYY-MM-DD HH:mm:ss") + " <time class='timeago timeago-contextual' datetime='" + moment().format("YYYY-MM-DD HH:mm:ss") + "'></time>");
				correspondingBookingsRoot.find(".undo-stock-booking-button").addClass("disabled");
				RefreshContextualTimeago("#stock-booking-" + bookingId + "-row");
				toastr.success(__t("Booking successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
				toastr.error(__t(JSON.parse(xhr.response).error_message));
			}
		);
	});

}


window.stockjournalView = stockjournalView
