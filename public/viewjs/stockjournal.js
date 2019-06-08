var stockJournalTable = $('#stock-journal-table').DataTable({
	'paginate': true,
	'order': [[3, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(__t('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	}
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

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	stockJournalTable.search(value).draw();
});

if (typeof GetUriParam("product") !== "undefined")
{
	$("#product-filter").val(GetUriParam("product"));
	$("#product-filter").trigger("change");
}

$(document).on('click', '.undo-stock-booking-button', function(e)
{
	e.preventDefault();

	var element = $(e.currentTarget);
	var bookingId = $(e.currentTarget).attr('data-booking-id');

	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', { },
		function(result)
		{
			element.closest("tr").addClass("text-muted");
			element.parent().siblings().find("span.name-anchor").addClass("text-strike-through").after("<br>" + __t("Undone on") + " " + moment().format("YYYY-MM-DD HH:mm:ss") + " <time class='timeago timeago-contextual' datetime='" + moment().format("YYYY-MM-DD HH:mm:ss") + "'></time>");
			element.closest(".undo-stock-booking-button").addClass("disabled");
			RefreshContextualTimeago();
			toastr.success(__t("Booking successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
			toastr.error(__t(JSON.parse(xhr.response).error_message));
		}
	);
});
