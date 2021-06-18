﻿var stockJournalTable = $('#stock-journal-table').DataTable({
	'paginate': true,
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
	var text = $("#product-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	stockJournalTable.column(1).search(text).draw();
});

$("#transaction-type-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#transaction-type-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	stockJournalTable.column(4).search(text).draw();
});

$("#location-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#location-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	stockJournalTable.column(5).search(text).draw();
});

$("#user-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#user-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	stockJournalTable.column(6).search(text).draw();
});

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	stockJournalTable.search(value).draw();
}, 200));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#transaction-type-filter").val("all");
	$("#location-filter").val("all");
	$("#user-filter").val("all");
	$("#product-filter").val("all");
	stockJournalTable.column(1).search("").draw();
	stockJournalTable.column(4).search("").draw();
	stockJournalTable.column(5).search("").draw();
	stockJournalTable.column(6).search("").draw();
	stockJournalTable.search("").draw();
});

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
