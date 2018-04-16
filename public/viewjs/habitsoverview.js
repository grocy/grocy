$('#habits-overview-table').DataTable({
	'pageLength': 50,
	'order': [[1, 'desc']],
	'language': JSON.parse(L('datatables_localization'))
});
