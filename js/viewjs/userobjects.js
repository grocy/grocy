function userobjectsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (scope) => $(scope).find(scope);
	}

	var userobjectsTable = $scope('#userobjects-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#userobjects-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(userobjectsTable);

	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to delete this userobject?',
		'.userobject-delete-button',
		'data-userobject-id',
		'data-userobject-id',
		'objects/userobjects/',
		() => window.location.reload()
	);
}


window.userobjectsView = userobjectsView
