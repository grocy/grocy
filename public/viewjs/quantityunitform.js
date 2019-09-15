$('#save-quantityunit-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#quantityunit-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("quantityunit-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/quantity_units', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/quantityunits');
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
					window.location.href = U('/quantityunits');
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
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(__t('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	}
});
$('#qu-conversions-table tbody').removeClass("d-none");
quConversionsTable.columns.adjust().draw();

Grocy.Components.UserfieldsForm.Load();
$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('quantityunit-form');

$(document).on('click', '.qu-conversion-delete-button', function(e)
{
	var objectId = $(e.currentTarget).attr('data-qu-conversion-id');

	bootbox.confirm({
		message: __t('Are you sure to remove this conversion?'),
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

$(document).on('click', '.qu-conversion-edit-button', function (e)
{
	var id = $(e.currentTarget).attr('data-qu-conversion-id');
	
	Grocy.Api.Put('objects/quantity_units/' + Grocy.EditObjectId, $('#quantityunit-form').serializeJSON(),
		function(result)
		{
			window.location.href = U("/quantityunitconversion/" + id.toString() + "?qu-unit=" + Grocy.EditObjectId.toString());
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$("#qu-conversion-add-button").on("click", function(e)
{
	Grocy.Api.Put('objects/quantity_units/' + Grocy.EditObjectId, $('#quantityunit-form').serializeJSON(),
		function(result)
		{
			window.location.href = U("/quantityunitconversion/new?qu-unit=" + Grocy.EditObjectId.toString());
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
