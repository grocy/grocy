import { WindowMessageBag } from '../helpers/messagebag';

function quantityunitformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var userfields = Grocy.Use("userfieldsform");

	$scope('.save-quantityunit-button').on('click', function(e)
	{
		e.preventDefault();

		var jsonData = $scope('#quantityunit-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("quantityunit-form");

		var redirectDestination = U('/quantityunits');
		if (Grocy.QuantityUnitEditFormRedirectUri !== undefined)
		{
			redirectDestination = Grocy.QuantityUnitEditFormRedirectUri;
		}

		if ($scope(e.currentTarget).attr('data-location') == "continue")
		{
			redirectDestination = "reload";
		}

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/quantity_units', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfields.Save(function()
					{
						if (Grocy.GetUriParam("embedded") !== undefined)
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
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			Grocy.Api.Put('objects/quantity_units/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					userfields.Save(function()
					{
						if (Grocy.GetUriParam("embedded") !== undefined)
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
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
	});

	$scope('#quantityunit-form input').keyup(function(event)
	{
		if (!$scope("#name").val().isEmpty())
		{
			$scope("#qu-conversion-headline-info").text(__t('1 %s is the same as...', $scope("#name").val()));
		}
		else
		{
			$scope("#qu-conversion-headline-info").text("");
		}

		if ($scope('quantityunit-form')[0].checkValidity() === false) //There is at least one validation error
		{
			$scope("#qu-conversion-add-button").addClass("disabled");
		}
		else
		{
			$scope("#qu-conversion-add-button").removeClass("disabled");
		}

		Grocy.FrontendHelpers.ValidateForm('quantityunit-form');
	});

	$scope('#quantityunit-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if ($scope('quantityunit-form')[0].checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-quantityunit-button').click();
			}
		}
	});

	var quConversionsTable = $scope('#qu-conversions-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#qu-conversions-table tbody').removeClass("d-none");
	quConversionsTable.columns.adjust().draw();

	userfields.Load();
	$scope("#name").trigger("keyup");
	$scope('#name').focus();
	Grocy.FrontendHelpers.ValidateForm('quantityunit-form');

	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to remove this conversion?',
		'.qu-conversion-delete-button',
		'data-qu-conversion-id',
		'data-qu-conversion-id',
		'objects/quantity_unit_conversions/',
		() => window.location.reload(),
	);

	// TODO: LoadSubView
	$scope("#test-quantityunit-plural-forms-button").on("click", function(e)
	{
		e.preventDefault();

		Grocy.QuantityUnitEditFormRedirectUri = "stay";
		$scope("#save-quantityunit-button").click();

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

}


window.quantityunitformView = quantityunitformView
