var stockJournalTable = $('#stock-journal-table').DataTable({
	'order': [
		[3, 'desc']
	],
	'columnDefs': [{
			'orderable': false,
			'targets': 0
		},
		{
			'searchable': false,
			"targets": 0
		}
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#stock-journal-table tbody').removeClass("d-none");
stockJournalTable.columns.adjust().draw();

$("#product-filter").select2({
	ajax: {
		delay: 150,
		transport: function(params, success, failure) {
			var results_per_page = 10;
			var page = params.data.page || 1;
			var term = params.data.term || "";

			var query = [];
			query.push('query%5B%5D=active%3D1');
			query.push('limit=' + encodeURIComponent(results_per_page));
			query.push('offset=' + encodeURIComponent((page - 1) * results_per_page));
			query.push('order=name%3Acollate%20nocase');
			if (term.length > 0) {
				query.push('search=' + encodeURIComponent(term));
			}

			Grocy.Api.Get('objects/products' + (query.length > 0 ? '?' + query.join('&') : ''),
				function(results, meta) {
					success({
						results: [{
							id: 'all',
							text: __t('All')
						}].concat(results.map(function(result) {
							return {
								id: result.id,
								text: result.name
							};
						})),
						pagination: {
							more: page * results_per_page < meta.recordsFiltered
						}
					});
				},
				function(xhr) {
					failure();
				}
			);
		}
	}
});

$("#product-filter").on("change", function() {
	var value = $(this).val();
	if (value === "all" && GetUriParam("product") !== undefined) {
		RemoveUriParam("product");
		window.location.reload();
	} else if (GetUriParam("product") !== value) {
		UpdateUriParam("product", value);
		window.location.reload();
	}
});

$("#transaction-type-filter").on("change", function() {
	var value = $(this).val();
	var text = $("#transaction-type-filter option:selected").text();
	if (value === "all") {
		text = "";
	}

	stockJournalTable.column(stockJournalTable.colReorder.transpose(4)).search(text).draw();
});

$("#location-filter").on("change", function() {
	var value = $(this).val();
	var text = $("#location-filter option:selected").text();
	if (value === "all") {
		text = "";
	}

	stockJournalTable.column(stockJournalTable.colReorder.transpose(5)).search(text).draw();
});

$("#user-filter").on("change", function() {
	var value = $(this).val();
	var text = $("#user-filter option:selected").text();
	if (value === "all") {
		text = "";
	}

	stockJournalTable.column(stockJournalTable.colReorder.transpose(6)).search(text).draw();
});

$("#daterange-filter").on("change", function() {
	UpdateUriParam("months", $(this).val());
	window.location.reload();
});

$("#search").on("keyup", Delay(function() {
	var value = $(this).val();
	if (value === "all") {
		value = "";
	}

	stockJournalTable.search(value).draw();
}, 200));

$("#clear-filter-button").on("click", function() {
	$("#search").val("");
	$("#transaction-type-filter").val("all");
	$("#location-filter").val("all");
	$("#user-filter").val("all");
	$("#product-filter").val("all");
	$("#daterange-filter").val("6");

	RemoveUriParam("months");
	RemoveUriParam("product");
	window.location.reload();
});

var prefillProductId = GetUriParam("product");
if (typeof prefillProductId !== "undefined") {
	if ($("#product-filter").find('option[value="' + prefillProductId + '"]').length) {
		$("#product-filter").val(prefillProductId).trigger('change');
	} else {
		Grocy.Api.Get('objects/products/' + encodeURIComponent(prefillProductId),
			function(result) {
				var option = new Option(result.name, prefillProductId, true, true);
				$("#product-filter").append(option).trigger('change');
			},
			function(xhr) {
				console.error(xhr);
			}
		);
	}
}

if (typeof GetUriParam("months") !== "undefined") {
	$("#daterange-filter").val(GetUriParam("months"));
}

$(document).on('click', '.undo-stock-booking-button', function(e) {
	e.preventDefault();

	var bookingId = $(e.currentTarget).attr('data-booking-id');
	var correlationId = $("#stock-booking-" + bookingId + "-row").attr("data-correlation-id");

	var correspondingBookingsRoot = $("#stock-booking-" + bookingId + "-row");
	if (!correlationId.isEmpty()) {
		correspondingBookingsRoot = $(".stock-booking-correlation-" + correlationId);
	}

	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
		function(result) {
			correspondingBookingsRoot.addClass("text-muted");
			correspondingBookingsRoot.find("span.name-anchor").addClass("text-strike-through").after("<br>" + __t("Undone on") + " " + moment().format("YYYY-MM-DD HH:mm:ss") + " <time class='timeago timeago-contextual' datetime='" + moment().format("YYYY-MM-DD HH:mm:ss") + "'></time>");
			correspondingBookingsRoot.find(".undo-stock-booking-button").addClass("disabled");
			RefreshContextualTimeago("#stock-booking-" + bookingId + "-row");
			toastr.success(__t("Booking successfully undone"));
		},
		function(xhr) {
			console.error(xhr);
			toastr.error(__t(JSON.parse(xhr.response).error_message));
		}
	);
});
