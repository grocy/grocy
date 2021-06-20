var choresTable = $('#chores-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#chores-table tbody').removeClass("d-none");
Grocy.FrontendHelpers.InitDataTable(choresTable, null, function()
{
	$("#search").val("");
	choresTable.search("").draw();
	$("#show-disabled").prop('checked', false);
});

Grocy.FrontendHelpers.MakeDeleteConfirmBox(
	'Are you sure to delete chore "%s"?',
	'.core-delete-button',
	'data-chore-name',
	'data-chore-id',
	'objects/chores/',
	'/chroes'
);

$("#show-disabled").change(function()
{
	if (this.checked)
	{
		window.location.href = U('/chores?include_disabled');
	}
	else
	{
		window.location.href = U('/chores');
	}
});

if (GetUriParam('include_disabled'))
{
	$("#show-disabled").prop('checked', true);
}
