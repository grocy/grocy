var apiKeysTable = $('#apikeys-table').DataTable({
	'order': [[4, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#apikeys-table tbody').removeClass("d-none");
apiKeysTable.columns.adjust().draw();

var createdApiKeyId = GetUriParam('CreatedApiKeyId');
if (createdApiKeyId !== undefined)
{
	animateCSS("#apiKeyRow_" + createdApiKeyId, "pulse");
}

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	apiKeysTable.search(value).draw();
}, 200));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	apiKeysTable.search("").draw();
});

$(document).on('click', '.apikey-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-apikey-apikey');
	var objectId = $(e.currentTarget).attr('data-apikey-id');

	bootbox.confirm({
		message: __t('Are you sure to delete API key "%s"?', objectName),
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
				Grocy.Api.Delete('objects/api_keys/' + objectId, {},
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

function QrCodeForApiKey(apiKeyType, apiKey)
{
	var content = U('/api') + '|' + apiKey;
	if (apiKeyType === 'special-purpose-calendar-ical')
	{
		content = U('/api/calendar/ical?secret=' + apiKey);
	}

	return QrCodeImgHtml(content);
}

$('.apikey-show-qr-button').on('click', function()
{
	var qrcodeHtml = QrCodeForApiKey($(this).data('apikey-type'), $(this).data('apikey-key'));
	bootbox.alert({
		title: __t('API key'),
		message: "<p class='text-center'>" + qrcodeHtml + "</p>",
		closeButton: false
	});
})
