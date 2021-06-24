function userentitiesView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}
	// preload some views.
	Grocy.PreloadView("userentityform")


	var userentitiesTable = $scope('#userentities-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#userentities-table tbody').removeClass("d-none");
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


window.userentitiesView = userentitiesView
