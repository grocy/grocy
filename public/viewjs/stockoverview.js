$('#stock-overview-table').DataTable({
	'pageLength': 50,
	'order': [[2, 'asc']],
	'language': JSON.parse(L('datatables_localization'))
});
