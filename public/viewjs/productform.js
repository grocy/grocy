﻿$('#save-product-button').on('click', function (e)
{
	e.preventDefault();

	var redirectDestination = U('/products');
	var returnTo = GetUriParam('returnto');
	if (returnTo !== undefined)
	{
		redirectDestination = returnTo + '?createdproduct=' + encodeURIComponent($('#name').val());
	}

	var jsonData = $('#product-form').serializeJSON();
	if ($("#product-picture")[0].files.length > 0)
	{
		var someRandomStuff = Math.random().toString(36).substring(2, 100) + Math.random().toString(36).substring(2, 100);
		jsonData.picture_file_name = someRandomStuff + $("#product-picture")[0].files[0].name;
	}

	if (Grocy.DeleteProductPictureOnSave)
	{
		jsonData.picture_file_name = null;
	}

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/products', jsonData,
			function (result)
			{
				if (jsonData.hasOwnProperty("picture_file_name") && !Grocy.DeleteProductPictureOnSave)
				{
					Grocy.Api.UploadFile($("#product-picture")[0].files[0], 'productpictures', jsonData.picture_file_name,
						function (result)
						{
							window.location.href = redirectDestination;
						},
						function (xhr)
						{
							Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
						}
					);
				}
				else
				{
					window.location.href = redirectDestination;
				}
			},
			function (xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		if (Grocy.DeleteProductPictureOnSave)
		{
			Grocy.Api.DeleteFile(Grocy.ProductPictureFileName, 'productpictures',
				function(result)
				{
					// Nothing to do
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		};

		Grocy.Api.Post('edit-object/products/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				if (jsonData.hasOwnProperty("picture_file_name") && !Grocy.DeleteProductPictureOnSave)
				{
					Grocy.Api.UploadFile($("#product-picture")[0].files[0], 'productpictures', jsonData.picture_file_name,
						function(result)
						{
							window.location.href = redirectDestination;
						},
						function(xhr)
						{
							Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
						}
					);
				}
				else
				{
					window.location.href = redirectDestination;
				}
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#barcode-taginput').tagsManager({
	'hiddenTagListName': 'barcode',
	'tagsContainer': '#barcode-taginput-container',
	'tagClass': 'badge badge-secondary'
});

if (Grocy.EditMode === 'edit')
{
	Grocy.Api.Get('get-object/products/' + Grocy.EditObjectId,
		function (product)
		{
			if (product.barcode !== null && product.barcode.length > 0)
			{
				product.barcode.split(',').forEach(function(item)
				{
					$('#barcode-taginput').tagsManager('pushTag', item);
				});
			}
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

var prefillName = GetUriParam('prefillname');
if (prefillName !== undefined)
{
	$('#name').val(prefillName);
	$('#name').focus();
}

var prefillBarcode = GetUriParam('prefillbarcode');
if (prefillBarcode !== undefined)
{
	$('#barcode-taginput').tagsManager('pushTag', prefillBarcode);
	$('#name').focus();
}

$('.input-group-qu').on('change', function(e)
{
	var quIdPurchase = $("#qu_id_purchase").val();
	var quIdStock = $("#qu_id_stock").val();
	var factor = $('#qu_factor_purchase_to_stock').val();

	if (quIdPurchase != quIdStock)
	{
		$('#qu_factor_purchase_to_stock').attr("min", 2);
		$("#qu_factor_purchase_to_stock").parent().find(".invalid-feedback").text(L('The amount cannot be lower than #1', '2'));
	}
	else
	{
		$('#qu_factor_purchase_to_stock').attr("min", 1);
		$("#qu_factor_purchase_to_stock").parent().find(".invalid-feedback").text(L('The amount cannot be lower than #1', '1'));
	}

	if (factor > 1)
	{
		$('#qu-conversion-info').text(L('This means 1 #1 purchased will be converted into #2 #3 in stock', $("#qu_id_purchase option:selected").text(), (1 * factor).toString(), $("#qu_id_stock option:selected").text()));
		$('#qu-conversion-info').removeClass('d-none');
	}
	else
	{
		$('#qu-conversion-info').addClass('d-none');
	}

	Grocy.FrontendHelpers.ValidateForm('product-form');
});

$('#product-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('product-form');
});

$('#product-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();
		
		if (document.getElementById('product-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-product-button').click();
		}
	}
});

Grocy.DeleteProductPictureOnSave = false;
$('#delete-current-product-picture-button').on('click', function (e)
{
	Grocy.DeleteProductPictureOnSave = true;
	$("#current-product-picture").addClass("d-none");
	$("#delete-current-product-picture-on-save-hint").removeClass("d-none");
	$("#delete-current-product-picture-button").addClass("disabled");
});

$('#name').focus();
$('.input-group-qu').trigger('change');
Grocy.FrontendHelpers.ValidateForm('product-form');
