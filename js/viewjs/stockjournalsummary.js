var journalSummaryTable = $('#stock-journal-summary-table').DataTable({
	'paginate': true,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#stock-journal-summary-table tbody').removeClass("d-none");
journalSummaryTable.columns.adjust().draw();

$("#product-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#product-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	journalSummaryTable.column(1).search(text).draw();
});

$("#transaction-type-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#transaction-type-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	journalSummaryTable.column(2).search(text).draw();
});

$("#user-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#user-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	journalSummaryTable.column(3).search(text).draw();
});

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	journalSummaryTable.search(value).draw();
}, 200));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#transaction-type-filter").val("all");
	$("#location-filter").val("all");
	$("#user-filter").val("all");
	$("#product-filter").val("all");
	journalSummaryTable.column(1).search("").draw();
	journalSummaryTable.column(2).search("").draw();
	journalSummaryTable.column(3).search("").draw();
	journalSummaryTable.search("").draw();
});
