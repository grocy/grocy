var stockJournalTable = $('#stock-journal-table').DataTable({
	'order': [[3, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#stock-journal-table tbody').removeClass("d-none");
stockJournalTable.columns.adjust().draw();

$("#product-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		RemoveUriParam("product");
	}
	else
	{
		UpdateUriParam("product", value);
	}

	window.location.reload();
});

$("#transaction-type-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#transaction-type-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	stockJournalTable.column(stockJournalTable.colReorder.transpose(4)).search(text).draw();
});

$("#location-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#location-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	stockJournalTable.column(stockJournalTable.colReorder.transpose(5)).search(text).draw();
});

$("#user-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#user-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	stockJournalTable.column(stockJournalTable.colReorder.transpose(6)).search(text).draw();
});

$("#daterange-filter").on("change", function()
{
	UpdateUriParam("months", $(this).val());
	window.location.reload();
});

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	stockJournalTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#transaction-type-filter").val("all");
	$("#location-filter").val("all");
	$("#user-filter").val("all");
	$("#daterange-filter").val("6");
	RemoveUriParam("months");

	if (GetUriParam("embedded") === undefined)
	{
		RemoveUriParam("product");
		$("#product-filter").val("all");
	}

	window.location.reload();
});

if (typeof GetUriParam("product") !== "undefined")
{
	$("#product-filter").val(GetUriParam("product"));
}

if (typeof GetUriParam("months") !== "undefined")
{
	$("#daterange-filter").val(GetUriParam("months"));
}

$(document).on('click', '.undo-stock-booking-button', function(e)
{
	e.preventDefault();

	var bookingId = $(e.currentTarget).attr('data-booking-id');
	var correlationId = $("#stock-booking-" + bookingId + "-row").attr("data-correlation-id");

	var correspondingBookingsRoot = $("#stock-booking-" + bookingId + "-row");
	if (correlationId)
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

$(document).on('click', '.product-grocycode-label-print', function(e)
{
	e.preventDefault();

	var productId = $(e.currentTarget).attr('data-product-id');
	Grocy.Api.Get('stock/products/' + productId + '/printlabel', function(labelData)
	{
		if (Grocy.Webhooks.labelprinter !== undefined)
		{
			Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, labelData);
		}
	});
});
