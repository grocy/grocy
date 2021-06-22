function userentitiesView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var userentitiesTable = $('#userentities-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$('#userentities-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(userentitiesTable);
	
	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to delete userentity "%s"?',
		'.userentity-delete-button',
		'data-userentity-name',
		'data-userentity-id',
		'objects/userentities/',
		'/userentities'
	);
}
