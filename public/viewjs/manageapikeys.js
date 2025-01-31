var apiKeysTable = $('#apikeys-table').DataTable({
	'order': [[6, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#apikeys-table tbody').removeClass("d-none");
apiKeysTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	apiKeysTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	apiKeysTable.search("").draw();
});

$(document).on('click', '.apikey-delete-button', function(e)
{
	var button = $(e.currentTarget);
	var objectName = button.attr('data-apikey-key');
	var objectDescription = button.attr('data-apikey-description');
	var objectId = button.attr('data-apikey-id');

	if (objectDescription)
	{
		objectName = objectDescription;
	}

	bootbox.confirm({
		message: __t('Are you sure you want to delete API key "%s"?', objectName),
		closeButton: false,
		className: "text-break",
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

$(".apikey-show-qr-button").on("click", function()
{
	var button = $(this);
	var apiKey = button.data("apikey-key");
	var apiKeyType = button.data("apikey-type");
	var apiKeyDescription = button.data("apikey-description");

	var content = U("/api") + "|" + apiKey;
	if (apiKeyType === "special-purpose-calendar-ical")
	{
		content = U("/api/calendar/ical?secret=" + apiKey);
	}

	bootbox.alert({
		message: "<div class='text-center'><h1>" + __t("API key") + "</h1><h2 class='text-muted'>" + apiKeyDescription + "</h2><p><hr>" + QrCodeImgHtml(content) + "</p></div>",
		closeButton: false
	});
});

$("#add-api-key-button").on("click", function(e)
{
	$("#add-api-key-modal").modal("show");
});

$("#add-api-key-modal").on("shown.bs.modal", function(e)
{
	setTimeout(function()
	{
		$("#description").focus();
	}, Grocy.FormFocusDelay);
});

$("#new-api-key-button").on("click", function(e)
{
	window.location.href = U("/manageapikeys/new?description=" + encodeURIComponent($("#description").val()));
});
