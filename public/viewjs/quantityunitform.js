$('#save-quantityunit-button').on('click', function(e)
{
	e.preventDefault();

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

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/quantity_units', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
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
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("quantityunit-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("quantityunit-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#quantityunit-form input').keyup(function(event)
{
	if (!$("#name").val().isEmpty())
	{
		$("#qu-conversion-headline-info").text(__t('1 %s is the same as...', $("#name").val()));
	}
	else
	{
		$("#qu-conversion-headline-info").text("");
	}

	if (document.getElementById('quantityunit-form').checkValidity() === false) //There is at least one validation error
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
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('quantityunit-form').checkValidity() === false) //There is at least one validation error
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
	]
});
$('#qu-conversions-table tbody').removeClass("d-none");
quConversionsTable.columns.adjust().draw();

Grocy.Components.UserfieldsForm.Load();
$("#name").trigger("keyup");
$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('quantityunit-form');

$(document).on('click', '.qu-conversion-delete-button', function(e)
{
	var objectId = $(e.currentTarget).attr('data-qu-conversion-id');

	bootbox.confirm({
		message: __t('Are you sure to remove this conversion?'),
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
				Grocy.Api.Delete('objects/quantity_unit_conversions/' + objectId, { },
					function(result)
					{
						Grocy.QuantityUnitEditFormRedirectUri = "reload";
						$('#save-quantityunit-button').click();
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

$(document).on('click', '.qu-conversion-edit-button', function (e)
{
	var id = $(e.currentTarget).attr('data-qu-conversion-id');
	Grocy.QuantityUnitEditFormRedirectUri = U("/quantityunitconversion/" + id.toString() + "?qu-unit=editobjectid");
	$('#save-quantityunit-button').click();
});

$("#qu-conversion-add-button").on("click", function(e)
{
	Grocy.QuantityUnitEditFormRedirectUri = U("/quantityunitconversion/new?qu-unit=editobjectid");
	$('#save-quantityunit-button').click();
});

$("#test-quantityunit-plural-forms-button").on("click", function(e)
{
	e.preventDefault();

	Grocy.QuantityUnitEditFormRedirectUri = "stay";
	$("#save-quantityunit-button").click();

	bootbox.alert({
		message: '<iframe height="400px" class="embed-responsive" src="' + U("/quantityunitpluraltesting?embedded&qu=") + Grocy.EditObjectId.toString() + '"></iframe>',
		closeButton: false,
		size: "large",
		callback: function(result)
		{
			Grocy.QuantityUnitEditFormRedirectUri = undefined;
			Grocy.FrontendHelpers.EndUiBusy("quantityunit-form");
		}
	});
});
