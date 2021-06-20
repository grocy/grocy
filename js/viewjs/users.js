var usersTable = $('#users-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#users-table tbody').removeClass("d-none");
Grocy.FrontendHelpers.InitDataTable(usersTable);

Grocy.FrontendHelpers.MakeDeleteConfirmBox(
	'Are you sure to delete user "%s"?',
	'.user-delete-button',
	'data-user-username',
	'data-user-id',
	'users/',
	'/users'
);