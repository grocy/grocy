function shoppinglocationsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var locationsTable = $('#shoppinglocations-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$('#shoppinglocations-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(locationsTable);
	
	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to delete store "%s"?',
		'.shoppinglocation-delete-button',
		'data-shoppinglocation-name',
		'data-shoppinglocation-id',
		'objects/shopping_locations/',
		'/shoppinglocations'
	);
}
