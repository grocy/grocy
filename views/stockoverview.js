$(function()
{
	$('#stock-overview-table').DataTable({
		'pageLength': 50,
		'order': [[2, 'asc']]
	});
});
