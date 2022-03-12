var journalSummaryTable = $('#stock-journal-summary-table').DataTable({
	'order': [
		[1, 'asc']
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
$('#stock-journal-summary-table tbody').removeClass("d-none");
journalSummaryTable.columns.adjust().draw();

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
	var text = $("#product-filter option:selected").text().trim();
	if (value === "all") {
		journalSummaryTable.column(journalSummaryTable.colReorder.transpose(1)).search("").draw();
	} else {
		journalSummaryTable.column(journalSummaryTable.colReorder.transpose(1)).search("^" + text + "$", true, false).draw();
	}
});

$("#transaction-type-filter").on("change", function() {
	var value = $(this).val();
	var text = $("#transaction-type-filter option:selected").text();
	if (value === "all") {
		text = "";
	}

	journalSummaryTable.column(journalSummaryTable.colReorder.transpose(2)).search(text).draw();
});

$("#user-filter").on("change", function() {
	var value = $(this).val();
	var text = $("#user-filter option:selected").text();
	if (value === "all") {
		text = "";
	}

	journalSummaryTable.column(journalSummaryTable.colReorder.transpose(3)).search(text).draw();
});

$("#search").on("keyup", Delay(function() {
	var value = $(this).val();
	if (value === "all") {
		value = "";
	}

	journalSummaryTable.search(value).draw();
}, 200));

$("#clear-filter-button").on("click", function() {
	$("#search").val("");
	$("#transaction-type-filter").val("all");
	$("#location-filter").val("all");
	$("#user-filter").val("all");
	$("#product-filter").val("all");
	journalSummaryTable.column(journalSummaryTable.colReorder.transpose(1)).search("").draw();
	journalSummaryTable.column(journalSummaryTable.colReorder.transpose(2)).search("").draw();
	journalSummaryTable.column(journalSummaryTable.colReorder.transpose(3)).search("").draw();
	journalSummaryTable.search("").draw();
});
