var journalSummaryTable = $('#journal-summary-table').DataTable({
	'paginate': true,
	'order': [[0, 'desc']]
});
$('#journal-summary-table tbody').removeClass("d-none");
journalSummaryTable.columns.adjust().draw();
