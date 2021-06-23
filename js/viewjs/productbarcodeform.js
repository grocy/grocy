import { WindowMessageBag } from '../helpers/messagebag';

function productbarcodeformView(Grocy, scope = null)
{
	var $scope = $;
	var top = scope != null ? $(scope) : $(document);

	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use('barcodescanner');
	var productamountpicker = Grocy.Use("productamountpicker");
	var userfields = Grocy.Use("userfieldsform");

	$scope('#save-barcode-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#barcode-form').serializeJSON();
		jsonData.amount = jsonData.display_amount;
		delete jsonData.display_amount;

		Grocy.FrontendHelpers.BeginUiBusy("barcode-form");

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/product_barcodes', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfields.Save()

					window.parent.postMessage(WindowMessageBag("ProductBarcodesChanged"), U("/product/" + Grocy.GetUriParam("product")));
					window.parent.postMessage(WindowMessageBag("CloseAllModals"), U("/product/" + Grocy.GetUriParam("product")));
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
			userfields.Save();
			Grocy.Api.Put('objects/product_barcodes/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					window.parent.postMessage(WindowMessageBag("ProductBarcodesChanged"), U("/product/" + Grocy.GetUriParam("product")));
					window.parent.postMessage(WindowMessageBag("CloseAllModals"), U("/product/" + Grocy.GetUriParam("product")));
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("barcode-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
				}
			);
		}
	});

	$scope('#barcode').on('keyup', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('barcode-form');
	});

	$scope('#qu_id').on('change', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('barcode-form');
	});

	$scope('#display_amount').on('keyup', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('barcode-form');
	});

	$scope('#barcode-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('barcode-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-barcode-button').click();
			}
		}
	});

	productamountpicker.Reload(Grocy.EditObjectProduct.id, Grocy.EditObjectProduct.qu_id_purchase);
	if (Grocy.EditMode == "edit")
	{
		$scope("#display_amount").val(Grocy.EditObject.amount);
		$scope(".input-group-productamountpicker").trigger("change");
		productamountpicker.SetQuantityUnit(Grocy.EditObject.qu_id);
	}

	Grocy.FrontendHelpers.ValidateForm('barcode-form');
	$scope('#barcode').focus();
	RefreshLocaleNumberInput();
	userfields.Load()

	top.on("Grocy.BarcodeScanned", function(e, barcode, target)
	{
		if (target !== "#barcode")
		{
			return;
		}

		$scope("#barcode").val(barcode);
	});

}

window.productbarcodeformView = productbarcodeformView