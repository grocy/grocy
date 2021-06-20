var userobjectsTable = $('#userobjects-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#userobjects-table tbody').removeClass("d-none");
Grocy.FrontendHelpers.InitDataTable(userobjectsTable);

Grocy.FrontendHelpers.MakeDeleteConfirmBox(
	'Are you sure to delete this userobject?',
	'.userobject-delete-button',
	'data-userobject-id',
	'data-userobject-id',
	'objects/userobjects/',
	() => window.location.reload()
);