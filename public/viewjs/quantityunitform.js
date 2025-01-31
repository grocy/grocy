$('.save-quantityunit-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("quantityunit-form", true))
	{
		return;
	}

	var jsonData = $('#quantityunit-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("quantityunit-form");

	if (Grocy.QuantityUnitEditFormRedirectUri !== undefined)
	{
		redirectDestination = Grocy.QuantityUnitEditFormRedirectUri;
	}
	else
	{
		redirectDestination = U('/quantityunits');
	}

	if ($(e.currentTarget).attr('data-location') == "continue")
	{
		redirectDestination = "reload";
	}

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/quantity_units', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
					}
					else
					{

						if (redirectDestination == "reload")
						{
							window.location.href = U("/quantityunit/" + result.created_object_id.toString());
						}
						else if (redirectDestination == "stay")
						{
							// Do nothing
						}
						else
						{
							window.location.href = redirectDestination.replace("editobjectid", Grocy.EditObjectId);
						}
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("quantityunit-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/quantity_units/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
					}
					else
					{

						if (redirectDestination == "reload")
						{
							window.location.reload();
						}
						else if (redirectDestination == "stay")
						{
							// Do nothing
						}
						else
						{
							window.location.href = redirectDestination.replace("editobjectid", Grocy.EditObjectId);
						}
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("quantityunit-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
});

$('#quantityunit-form input').keyup(function(event)
{
	if ($("#name").val())
	{
		$("#qu-conversion-headline-info").text(__t('1 %s is the same as...', $("#name").val()));
	}
	else
	{
		$("#qu-conversion-headline-info").text("");
	}

	if (!Grocy.FrontendHelpers.ValidateForm('quantityunit-form'))
	{
		$("#qu-conversion-add-button").addClass("disabled");
	}
	else
	{
		$("#qu-conversion-add-button").removeClass("disabled");
	}

	Grocy.FrontendHelpers.ValidateForm('quantityunit-form');
});

$('#quantityunit-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('quantityunit-form'))
		{
			return false;
		}
		else
		{
			$('#save-quantityunit-button').click();
		}
	}
});

var quConversionsTable = $('#qu-conversions-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#qu-conversions-table tbody').removeClass("d-none");
quConversionsTable.columns.adjust().draw();

Grocy.Components.UserfieldsForm.Load();
$("#name").trigger("keyup");
Grocy.FrontendHelpers.ValidateForm('quantityunit-form');
setTimeout(function()
{
	$('#name').focus();
}, Grocy.FormFocusDelay);

$(document).on('click', '.qu-conversion-delete-button', function(e)
{
	var objectId = $(e.currentTarget).attr('data-qu-conversion-id');

	bootbox.confirm({
		message: __t('Are you sure you want to remove this conversion?'),
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
				Grocy.Api.Delete('objects/quantity_unit_conversions/' + objectId, {},
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

$("#test-quantityunit-plural-forms-button").on("click", function(e)
{
	e.preventDefault();

	Grocy.QuantityUnitEditFormRedirectUri = "stay";
	$("#save-quantityunit-button").click();

	bootbox.alert({
		message: '<iframe class="embed-responsive" src="' + U("/quantityunitpluraltesting?embedded&qu=") + Grocy.EditObjectId.toString() + '"></iframe>',
		closeButton: false,
		size: "large",
		callback: function(result)
		{
			Grocy.QuantityUnitEditFormRedirectUri = undefined;
			Grocy.FrontendHelpers.EndUiBusy("quantityunit-form");
		}
	});
});
