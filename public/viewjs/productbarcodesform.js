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
				Grocy.EditObjectId = result.created_object_id;
				window.location.href = U("/product/" + GetUriParam("product"));
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
				window.location.href = U("/product/" + GetUriParam("product"));
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("barcode-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}

	Grocy.Api.Get('stock/products/' + jsonData.product_id,
		function(productDetails)
		{
			var existingBarcodes = productDetails.product.barcode || '';
			if (existingBarcodes.length === 0)
			{
				productDetails.product.barcode = jsonData.barcode;
			}
			else
			{
				productDetails.product.barcode += ',' + jsonData.barcode;
			}
			var jsonDataProduct = {};
			jsonDataProduct.barcode = productDetails.product.barcode;

			Grocy.Api.Put('objects/products/' + jsonData.product_id, jsonDataProduct,
				function(result)
				{
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("barcode-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("barcode-form");
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
		}
	);
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
