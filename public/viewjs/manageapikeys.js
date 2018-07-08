$(document).on('click', '.apikey-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-apikey-apikey');
	var objectId = $(e.currentTarget).attr('data-apikey-id');

	bootbox.confirm({
		message: L('Are you sure to delete API key "#1"?', objectName),
		buttons: {
			confirm: {
				label: L('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: L('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Get('delete-object/api_keys/' + objectId,
					function(result)
					{
						window.location.href = U('/manageapikeys');
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

$('#apikeys-table').DataTable({
	'bPaginate': false,
	'order': [[4, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});

var createdApiKeyId = GetUriParam('CreatedApiKeyId');
if (createdApiKeyId !== undefined)
{
	$('#apiKeyRow_' + createdApiKeyId).effect('highlight', { }, 3000);
}
