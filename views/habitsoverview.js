$(function()
{
	$('#habits-overview-table').DataTable({
		'pageLength': 50,
		'order': [[1, 'desc']]
	});
});
