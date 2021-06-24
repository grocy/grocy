function stockjournalsummaryView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	var journalSummaryTable = $scope('#stock-journal-summary-table').DataTable({
		'paginate': true,
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#stock-journal-summary-table tbody').removeClass("d-none");

	Grocy.FrontendHelpers.InitDataTable(journalSummaryTable);

	Grocy.FrontendHelpers.MakeFilterForColumn("#product-filter", 1, journalSummaryTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#transaction-type-filter", 2, journalSummaryTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#user-filter", 3, journalSummaryTable);
}


window.stockjournalsummaryView = stockjournalsummaryView
