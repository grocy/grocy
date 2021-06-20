var choresTable = $('#chores-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#chores-table tbody').removeClass("d-none");
Grocy.FrontendHelpers.InitDataTable(choresTable, null, function()
{
	$("#search").val("");
	choresTable.search("").draw();
	$("#show-disabled").prop('checked', false);
});

$(document).on('click', '.chore-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-chore-name');
	var objectId = $(e.currentTarget).attr('data-chore-id');

	bootbox.confirm({
		message: __t('Are you sure to delete chore "%s"?', objectName),
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
				Grocy.Api.Delete('objects/chores/' + objectId, {},
					function(result)
					{
						window.location.href = U('/chores');
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

$("#show-disabled").change(function()
{
	if (this.checked)
	{
		window.location.href = U('/chores?include_disabled');
	}
	else
	{
		window.location.href = U('/chores');
	}
});

if (GetUriParam('include_disabled'))
{
	$("#show-disabled").prop('checked', true);
}
