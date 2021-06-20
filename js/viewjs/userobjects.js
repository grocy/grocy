﻿var userobjectsTable = $('#userobjects-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#userobjects-table tbody').removeClass("d-none");
Grocy.FrontendHelpers.InitDataTable(userobjectsTable);

$(document).on('click', '.userobject-delete-button', function(e)
{
	var objectId = $(e.currentTarget).attr('data-userobject-id');

	bootbox.confirm({
		message: __t('Are you sure to delete this userobject?'),
		closeButton: false,
		buttons: {
			confirm: {
				label: __t('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: __t('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/userobjects/' + objectId, {},
					function(result)
					{
						window.location.reload();
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
