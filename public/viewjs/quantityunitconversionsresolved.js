var quConversionsResolvedTable = $('#qu-conversions-resolved-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#qu-conversions-resolved-table tbody').removeClass("d-none");
quConversionsResolvedTable.columns.adjust().draw();

$("#quantity-unit-filter").on("change", function()
{
	var value = $("#quantity-unit-filter option:selected").text();
	if (value === __t("All"))
	{
		value = "";
	}

	quConversionsResolvedTable.column([quConversionsResolvedTable.colReorder.transpose(1), quConversionsResolvedTable.colReorder.transpose(2)]).search(value).draw();
});

$("#clear-filter-button").on("click", function()
{
	$("#quantity-unit-filter").val("all");
	quConversionsResolvedTable.column([quConversionsResolvedTable.colReorder.transpose(1), quConversionsResolvedTable.colReorder.transpose(2)]).search("").draw();
	quConversionsResolvedTable.search("").draw();
});
