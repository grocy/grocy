$('#save-product-button').on('click', function(e)
{
	e.preventDefault();

	var redirectDestination = U('/products');
	var returnTo = GetUriParam('returnto');
	if (returnTo !== undefined)
	{
		redirectDestination = U(returnTo) + '?createdproduct=' + encodeURIComponent($('#name').val());
	}

	if (Grocy.ProductEditFormRedirectUri !== undefined)
	{
		redirectDestination = Grocy.ProductEditFormRedirectUri;
	}

	var jsonData = $('#product-form').serializeJSON({ checkboxUncheckedValue: "0" });
	var parentProductId = jsonData.product_id;
	delete jsonData.product_id;
	jsonData.parent_product_id = parentProductId;
	Grocy.FrontendHelpers.BeginUiBusy("product-form");

	if (jsonData.parent_product_id.toString().isEmpty())
	{
		jsonData.parent_product_id = null;
	}

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
		Grocy.Api.Post('objects/products', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (jsonData.hasOwnProperty("picture_file_name") && !Grocy.DeleteProductPictureOnSave)
					{
						Grocy.Api.UploadFile($("#product-picture")[0].files[0], 'productpictures', jsonData.picture_file_name,
							function(result)
							{
								if (GetUriParam("closeAfterCreation") !== undefined)
								{
									window.close();
								}
								else if (redirectDestination == "reload")
								{
									window.location.reload();
								}
								else
								{
									window.location.href = redirectDestination.replace("editobjectid", Grocy.EditObjectId);;
								}
							},
							function (xhr)
							{
								Grocy.FrontendHelpers.EndUiBusy("product-form");
								Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
							}
						);
					}
					else
					{
						if (GetUriParam("closeAfterCreation") !== undefined)
						{
							window.close();
						}
						else if (redirectDestination == "reload")
						{
							window.location.reload();
						}
						else
						{
							window.location.href = redirectDestination.replace("editobjectid", Grocy.EditObjectId);;
						}
					}
				});
			},
			function (xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("product-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		if (Grocy.DeleteProductPictureOnSave)
		{
			Grocy.Api.DeleteFile(Grocy.ProductPictureFileName, 'productpictures', {},
				function(result)
				{
					// Nothing to do
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("product-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		};

		Grocy.Api.Put('objects/products/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (jsonData.hasOwnProperty("picture_file_name") && !Grocy.DeleteProductPictureOnSave)
					{
						Grocy.Api.UploadFile($("#product-picture")[0].files[0], 'productpictures', jsonData.picture_file_name,
							function(result)
							{
								if (GetUriParam("closeAfterCreation") !== undefined)
								{
									window.close();
								}
								else if (redirectDestination == "reload")
								{
									window.location.reload();
								}
								else
								{
									window.location.href = redirectDestination.replace("editobjectid", Grocy.EditObjectId);;
								}
							},
							function(xhr)
							{
								Grocy.FrontendHelpers.EndUiBusy("product-form");
								Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
							}
						);
					}
					else
					{
						if (GetUriParam("closeAfterCreation") !== undefined)
						{
							window.close();
						}
						else if (redirectDestination == "reload")
						{
							window.location.reload();
						}
						else
						{
							window.location.href = redirectDestination.replace("editobjectid", Grocy.EditObjectId);;
						}
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("product-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#barcode-taginput').tagsManager({
	'hiddenTagListName': 'barcode',
	'tagsContainer': '#barcode-taginput-container',
	'tagClass': 'badge badge-secondary',
	'delimiters': [13, 44]
});

if (Grocy.EditMode === 'edit')
{
	Grocy.Api.Get('objects/products/' + Grocy.EditObjectId,
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

$("#barcode-taginput").on("blur", function(e)
{
	$("#barcode-taginput").tagsManager("pushTag", $("#barcode-taginput").val());
});

$('.input-group-qu').on('change', function(e)
{
	var quIdPurchase = $("#qu_id_purchase").val();
	var quIdStock = $("#qu_id_stock").val();
	var factor = $('#qu_factor_purchase_to_stock').val();

	if (factor > 1 || quIdPurchase != quIdStock)
	{
		$('#qu-conversion-info').text(__t('This means 1 %1$s purchased will be converted into %2$s %3$s in stock', $("#qu_id_purchase option:selected").text(), (1 * factor).toString(), __n((1 * factor).toString(), $("#qu_id_stock option:selected").text(), $("#qu_id_stock option:selected").data("plural-form"))));
		$('#qu-conversion-info').removeClass('d-none');
	}
	else
	{
		$('#qu-conversion-info').addClass('d-none');
	}

	$("#qu-conversion-headline-info").text(__t('1 %s is the same as...', $("#qu_id_stock option:selected").text()));
	quConversionsTable.column(4).search("from_qu_id xx" + $("#qu_id_stock").val().toString() + "xx").draw();

	$("#tare_weight_qu_info").text($("#qu_id_stock option:selected").text());

	Grocy.FrontendHelpers.ValidateForm('product-form');
});

$('#product-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('product-form');
	$(".input-group-qu").trigger("change");

	if (document.getElementById('product-form').checkValidity() === false) //There is at least one validation error
	{
		$("#qu-conversion-add-button").addClass("disabled");
	}
	else
	{
		$("#qu-conversion-add-button").removeClass("disabled");
	}
});

$('#product-form select').change(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('product-form');

	if (document.getElementById('product-form').checkValidity() === false) //There is at least one validation error
	{
		$("#qu-conversion-add-button").addClass("disabled");
	}
	else
	{
		$("#qu-conversion-add-button").removeClass("disabled");
	}
});

$('#location_id').change(function(event)
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

$("#enable_tare_weight_handling").on("click", function()
{
	if (this.checked)
	{
		$("#tare_weight").removeAttr("disabled");
	}
	else
	{
		$("#tare_weight").attr("disabled", "");
	}

	Grocy.FrontendHelpers.ValidateForm("product-form");
});

$("#allow_partial_units_in_stock").on("click", function()
{
	if (this.checked)
	{
		$("#min_stock_amount").attr("min", "0.0000");
		$("#min_stock_amount").attr("step", "0.0001");
		$("#qu_factor_purchase_to_stock").attr("min", "0.0001");
		$("#qu_factor_purchase_to_stock").attr("step", "0.0001");
		$("#qu_factor_purchase_to_stock").parent().find(".invalid-feedback").text(__t('This cannot be lower than %1$s and must be a valid number with max. %2$s decimal places', 0.0001.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 4 }), '4'));
	}
	else
	{
		$("#min_stock_amount").attr("min", "0");
		$("#min_stock_amount").attr("step", "1");
		$("#qu_factor_purchase_to_stock").attr("min", "1");
		$("#qu_factor_purchase_to_stock").attr("step", "1");
		$("#qu_factor_purchase_to_stock").parent().find(".invalid-feedback").text(__t('This cannot be lower than %1$s and must be a valid number with max. %2$s decimal places', '1', '0'));
	}

	Grocy.FrontendHelpers.ValidateForm("product-form");
});

Grocy.DeleteProductPictureOnSave = false;
$('#delete-current-product-picture-button').on('click', function (e)
{
	Grocy.DeleteProductPictureOnSave = true;
	$("#current-product-picture").addClass("d-none");
	$("#delete-current-product-picture-on-save-hint").removeClass("d-none");
	$("#delete-current-product-picture-button").addClass("disabled");
});

if (Grocy.EditMode === 'create')
{
	if (Grocy.UserSettings.product_presets_location_id.toString() !== '-1')
	{
		$("#location_id").val(Grocy.UserSettings.product_presets_location_id);
	}

	if (Grocy.UserSettings.product_presets_product_group_id.toString() !== '-1')
	{
		$("#product_group_id").val(Grocy.UserSettings.product_presets_product_group_id);
	}

	if (Grocy.UserSettings.product_presets_qu_id.toString() !== '-1')
	{
		$("select.input-group-qu").val(Grocy.UserSettings.product_presets_qu_id);
	}
}

var quConversionsTable = $('#qu-conversions-table').DataTable({
	'order': [[1, 'asc']],
	"orderFixed": [[3, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 3 }
	],
	'rowGroup': {
		dataSrc: 3
	}
});
$('#qu-conversions-table tbody').removeClass("d-none");
quConversionsTable.columns.adjust().draw();

Grocy.Components.UserfieldsForm.Load();
$("#name").trigger("keyup");
$('#name').focus();
$('.input-group-qu').trigger('change');
Grocy.FrontendHelpers.ValidateForm('product-form');

// Click twice to trigger on-click but not change the actual checked state
$("#allow_partial_units_in_stock").click();
$("#allow_partial_units_in_stock").click();

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
						Grocy.ProductEditFormRedirectUri = "reload";
						$('#save-product-button').click();
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
	Grocy.ProductEditFormRedirectUri = U("/quantityunitconversion/" + id.toString() + "?product=editobjectid");
	$('#save-product-button').click();
});

$("#qu-conversion-add-button").on("click", function(e)
{
	Grocy.ProductEditFormRedirectUri = U("/quantityunitconversion/new?product=editobjectid");
	$('#save-product-button').click();
});

$('#qu_id_purchase').blur(function(e) 
{
	// Preset the stock quantity unit with the purchase quantity unit, if the stock quantity unit is unset.
	var QuIdStock = $('#qu_id_stock');
	var QuIdPurchase = $('#qu_id_purchase');
	if (QuIdStock[0].selectedIndex === 0 && QuIdPurchase[0].selectedIndex !== 0) {
		QuIdStock[0].selectedIndex = QuIdPurchase[0].selectedIndex;
		Grocy.FrontendHelpers.ValidateForm('product-form');
	}
});

$(document).on("Grocy.BarcodeScanned", function(e, barcode, target)
{
	if (target != "#barcode-taginput")
	{
		return;
	}
	
	$("#barcode-taginput").tagsManager("pushTag", barcode);
});
