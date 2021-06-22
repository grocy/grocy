function stockjournalView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var stockJournalTable = $('#stock-journal-table').DataTable({
		'paginate': true,
		'order': [[3, 'desc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$('#stock-journal-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(stockJournalTable);
	
	Grocy.FrontendHelpers.MakeFilterForColumn("#product-filter", 1, stockJournalTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#transaction-type-filter", 4, stockJournalTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#location-filter", 5, stockJournalTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#user-filter", 6, stockJournalTable);
	
	if (typeof GetUriParam("product") !== "undefined")
	{
		$("#product-filter").val(GetUriParam("product"));
		$("#product-filter").trigger("change");
	}
	
	$(document).on('click', '.undo-stock-booking-button', function(e)
	{
		e.preventDefault();
	
		var bookingId = $(e.currentTarget).attr('data-booking-id');
		var correlationId = $("#stock-booking-" + bookingId + "-row").attr("data-correlation-id");
	
		var correspondingBookingsRoot = $("#stock-booking-" + bookingId + "-row");
		if (!correlationId.isEmpty())
		{
			correspondingBookingsRoot = $(".stock-booking-correlation-" + correlationId);
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
