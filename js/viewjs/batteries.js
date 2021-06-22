function batteriesView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var batteriesTable = $('#batteries-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 },
			{ "type": "num", "targets": 4 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$('#batteries-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(batteriesTable, null, function()
	{
		$("#search").val("");
		batteriesTable.search("").draw();
		$("#show-disabled").prop('checked', false);
	});
	
	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to delete battery "%s"?',
		'.battery-delete-button',
		'data-battery-name',
		'data-battery-id',
		'objects/batteries/',
		'/batteries'
	);
	
	$("#show-disabled").change(function()
	{
		if (this.checked)
		{
			window.location.href = U('/batteries?include_disabled');
		}
		else
		{
			window.location.href = U('/batteries');
		}
	});
	
	if (GetUriParam('include_disabled'))
	{
		$("#show-disabled").prop('checked', true);
	}
	
}
