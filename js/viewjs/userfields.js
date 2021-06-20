var userfieldsTable = $('#userfields-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#userfields-table tbody').removeClass("d-none");

Grocy.FrontendHelpers.InitDataTable(userfieldsTable, null, function()
{
	$("#search").val("");
	$("#entity-filter").val("all");
	userfieldsTable.column(1).search("").draw();
	userfieldsTable.search("").draw();
});

$("#entity-filter").on("change", function()
{
	var value = $("#entity-filter option:selected").text();
	if (value === __t("All"))
	{
		value = "";
	}

	userfieldsTable.column(1).search(value).draw();
	$("#new-userfield-button").attr("href", U("/userfield/new?embedded&entity=" + value));
});

Grocy.FrontendHelpers.MakeDeleteConfirmBox(
	'Are you sure to delete user field "%s"?',
	'.userfield-delete-button',
	'data-userfield-name',
	'data-userfield-id',
	'objects/userfields/',
	'/userfields'
);

if (GetUriParam("entity") != undefined && !GetUriParam("entity").isEmpty())
{
	$("#entity-filter").val(GetUriParam("entity"));
	$("#entity-filter").trigger("change");
	$("#name").focus();
}
