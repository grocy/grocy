$('#save-barcode-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#barcode-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("barcode-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/product_barcodes', jsonData,
			function(result)
			{
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
		Grocy.Api.Put('objects/product_barcodes/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("barcode-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}

	window.parent.postMessage(WindowMessageBag("ProductBarcodesChanged"), U("/product/" + GetUriParam("product")));
	window.parent.postMessage(WindowMessageBag("CloseAllModals"), U("/product/" + GetUriParam("product")));
});

$('#barcode').on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('barcode-form');
});

$('#qu_factor_purchase_to_stock').on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('barcode-form');
});

$('#barcode-form input').keydown(function(event)
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
			$('#save-barcode-button').click();
		}
	}
});
Grocy.FrontendHelpers.ValidateForm('barcode-form');
