$('#save-barcode-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("barcode-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#barcode-form').serializeJSON();
	jsonData.amount = jsonData.display_amount;
	delete jsonData.display_amount;
	jsonData.qu_id = $("#qu_id").val();

	Grocy.FrontendHelpers.BeginUiBusy("barcode-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/product_barcodes', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save()

				window.parent.postMessage(WindowMessageBag("ProductBarcodesChanged"), Grocy.BaseUrl);
				window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("barcode-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
	else
	{
		Grocy.Components.UserfieldsForm.Save();
		Grocy.Api.Put('objects/product_barcodes/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.parent.postMessage(WindowMessageBag("ProductBarcodesChanged"), Grocy.BaseUrl);
				window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("barcode-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
});

$('#barcode').on('keyup', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('barcode-form');
});

$('#qu_id').on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('barcode-form');
});

$('#display_amount').on('keyup', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('barcode-form');
});

$('#barcode-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('barcode-form'))
		{
			return false;
		}
		else
		{
			$('#save-barcode-button').click();
		}
	}
});

Grocy.Components.ProductAmountPicker.Reload(Grocy.EditObjectProduct.id, Grocy.EditObjectProduct.qu_id_purchase);
if (Grocy.EditMode == "edit")
{
	$("#display_amount").val(Grocy.EditObject.amount);
	$(".input-group-productamountpicker").trigger("change");

	if (Grocy.EditObject.qu_id)
	{
		Grocy.Components.ProductAmountPicker.SetQuantityUnit(Grocy.EditObject.qu_id);
	}
}

Grocy.FrontendHelpers.ValidateForm('barcode-form');
setTimeout(function()
{
	$('#barcode').focus();
}, Grocy.FormFocusDelay);
RefreshLocaleNumberInput();
Grocy.Components.UserfieldsForm.Load()

$(document).on("Grocy.BarcodeScanned", function(e, barcode, target)
{
	if (target !== "#barcode")
	{
		return;
	}

	$("#barcode").val(barcode);
});
