$(document).on('click', '.quantityunit-delete-button', function(e)
{
	bootbox.confirm({
		message: L('Are you sure to delete quantity unit "#1"?', $(e.currentTarget).attr('data-quantityunit-name')),
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Get('delete-object/quantity_units/' + $(e.currentTarget).attr('data-quantityunit-id'),
					function(result)
					{
						window.location.href = U('/quantityunits');
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		}
	});
});

$('#quantityunits-table').DataTable({
	'pageLength': 50,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});
