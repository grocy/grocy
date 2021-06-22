function locationsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var locationsTable = $('#locations-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$('#locations-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(locationsTable);
	
	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to delete location "%s"?',
		'.location-delete-button',
		'data-location-name',
		'data-location-id',
		'objects/locations/',
		'/locations'
	);
}
