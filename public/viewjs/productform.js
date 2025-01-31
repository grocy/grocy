function saveProductPicture(result, location, jsonData)
{
	var productId = Grocy.EditObjectId || result.created_object_id;
	Grocy.EditObjectId = productId; // Grocy.EditObjectId is not yet set when adding a product

	Grocy.Components.UserfieldsForm.Save(() =>
	{
		if (jsonData.hasOwnProperty("picture_file_name") && !Grocy.DeleteProductPictureOnSave)
		{
			Grocy.Api.UploadFile($("#product-picture")[0].files[0], 'productpictures', jsonData.picture_file_name,
				(result) =>
				{
					if (Grocy.ProductEditFormRedirectUri == "reload")
					{
						window.location.reload();
						return;
					}

					var returnTo = GetUriParam('returnto');
					if (GetUriParam("closeAfterCreation") !== undefined)
					{
						window.close();
					}
					else if (returnTo !== undefined)
					{
						if (GetUriParam("flow") !== undefined)
						{
							window.location.href = U(returnTo) + '&product-name=' + encodeURIComponent($('#name').val());
						}
						else
						{
							window.location.href = U(returnTo);
						}

					}
					else
					{
						window.location.href = U(location + productId);
					}

				},
				(xhr) =>
				{
					Grocy.FrontendHelpers.EndUiBusy("product-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
				}
			);
		}
		else
		{
			if (Grocy.ProductEditFormRedirectUri == "reload")
			{
				window.location.reload();
				return
			}

			var returnTo = GetUriParam('returnto');
			if (GetUriParam("closeAfterCreation") !== undefined)
			{
				window.close();
			}
			else if (returnTo !== undefined)
			{
				if (GetUriParam("flow") !== undefined)
				{
					window.location.href = U(returnTo) + '&product-name=' + encodeURIComponent($('#name').val());
				}
				else
				{
					window.location.href = U(returnTo);
				}
			}
			else
			{
				window.location.href = U(location + productId);
			}
		}
	});
}

$('.save-product-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("product-form", true))
	{
		return;
	}

	var jsonData = $('#product-form').serializeJSON();
	var parentProductId = jsonData.product_id;
	delete jsonData.product_id;
	jsonData.parent_product_id = parentProductId;
	Grocy.FrontendHelpers.BeginUiBusy("product-form");

	if ($("#product-picture")[0].files.length > 0)
	{
		jsonData.picture_file_name = RandomString() + CleanFileName($("#product-picture")[0].files[0].name);
	}

	const location = $(e.currentTarget).attr('data-location') == 'return' ? '/products?product=' : '/product/';

	if (Grocy.EditMode == 'create')
	{
		Grocy.Api.Post('objects/products', jsonData,
			(result) => saveProductPicture(result, location, jsonData),
			(xhr) =>
			{
				Grocy.FrontendHelpers.EndUiBusy("product-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			});
		return;
	}

	if (Grocy.DeleteProductPictureOnSave)
	{
		jsonData.picture_file_name = null;

		Grocy.Api.DeleteFile(Grocy.ProductPictureFileName, 'productpictures',
			function(result)
			{
				// Nothing to do
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("product-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}

	Grocy.Api.Put('objects/products/' + Grocy.EditObjectId, jsonData,
		(result) => saveProductPicture(result, location, jsonData),
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("product-form");
			console.error(xhr);
		}
	);
});

if (GetUriParam("flow") == "InplaceNewProductWithName")
{
	$('#name').val(GetUriParam("name"));
}

if (GetUriParam("flow") !== undefined || GetUriParam("returnto") !== undefined)
{
	$("#save-hint").addClass("d-none");
	$(".save-product-button[data-location='return']").addClass("d-none");
}

$('.input-group-qu').on('change', function(e)
{
	$("#tare_weight_qu_info").text($("#qu_id_stock option:selected").text());
	$("#quick_consume_qu_info").text($("#qu_id_stock option:selected").text());
	$("#quick_open_qu_info").text($("#qu_id_stock option:selected").text());
	$("#energy_qu_info").text(Grocy.EnergyUnit + " / " + $("#qu_id_stock option:selected").text());

	Grocy.FrontendHelpers.ValidateForm('product-form');
});

$('#product-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('product-form');
	$(".input-group-qu").trigger("change");
	$("#product-form select").trigger("select");

	if (!Grocy.FrontendHelpers.ValidateForm('product-form'))
	{
		$("#qu-conversion-add-button").addClass("disabled");
		$("#barcode-add-button").addClass("disabled");
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
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('product-form'))
		{
			return false;
		}
		else
		{
			$('.default-submit-button').click();
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

$("#product-picture").on("change", function(e)
{
	$("#product-picture-label").removeClass("d-none");
	$("#product-picture-label-none").addClass("d-none");
	$("#delete-current-product-picture-on-save-hint").addClass("d-none");
	$("#current-product-picture").addClass("d-none");
	Grocy.DeleteProductPictureOnSave = false;
});

Grocy.DeleteProductPictureOnSave = false;
$("#delete-current-product-picture-button").on("click", function(e)
{
	Grocy.DeleteProductPictureOnSave = true;
	$("#current-product-picture").addClass("d-none");
	$("#delete-current-product-picture-on-save-hint").removeClass("d-none");
	$("#product-picture-label").addClass("d-none");
	$("#product-picture-label-none").removeClass("d-none");
});

var quConversionsTable = $('#qu-conversions-table-products').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#qu-conversions-table-products tbody').removeClass("d-none");
quConversionsTable.columns.adjust().draw();

var barcodeTable = $('#barcode-table').DataTable({
	'order': [[1, 'asc']],
	"orderFixed": [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 5 },
		{ 'visible': false, 'targets': 6 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#barcode-table tbody').removeClass("d-none");
barcodeTable.columns.adjust().draw();

Grocy.Components.UserfieldsForm.Load();
$("#name").trigger("keyup");
$('.input-group-qu').trigger('change');
Grocy.FrontendHelpers.ValidateForm('product-form');
setTimeout(function()
{
	$('#name').focus();
}, Grocy.FormFocusDelay);

$(document).on('click', '.product-grocycode-label-print', function(e)
{
	e.preventDefault();

	var productId = $(e.currentTarget).attr('data-product-id');
	Grocy.Api.Get('stock/products/' + productId + '/printlabel', function(labelData)
	{
		if (Grocy.Webhooks.labelprinter !== undefined)
		{
			Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, labelData);
		}
	});
});

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

$(document).on('click', '.barcode-delete-button', function(e)
{
	var objectId = $(e.currentTarget).attr('data-barcode-id');

	bootbox.confirm({
		message: __t('Are you sure you want to remove this barcode?'),
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
				Grocy.Api.Delete('objects/product_barcodes/' + objectId, {},
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

$('#qu_id_stock').change(function(e)
{
	// Preset qu_id_purchase/qu_id_consume/qu_id_price by qu_id_stock if unset
	var quIdStock = $('#qu_id_stock');
	var quIdPurchase = $('#qu_id_purchase');
	var quIdConsume = $('#qu_id_consume');
	var quIdPrice = $('#qu_id_price');

	if (quIdPurchase[0].selectedIndex === 0 && quIdStock[0].selectedIndex !== 0)
	{
		quIdPurchase[0].selectedIndex = quIdStock[0].selectedIndex;
	}

	if (quIdConsume[0].selectedIndex === 0 && quIdStock[0].selectedIndex !== 0)
	{
		quIdConsume[0].selectedIndex = quIdStock[0].selectedIndex;
	}

	if (quIdPrice[0].selectedIndex === 0 && quIdStock[0].selectedIndex !== 0)
	{
		quIdPrice[0].selectedIndex = quIdStock[0].selectedIndex;
	}

	Grocy.FrontendHelpers.ValidateForm('product-form');
});

$(window).on("message", function(e)
{
	var data = e.originalEvent.data;

	if (data.Message === "ProductBarcodesChanged" || data.Message === "ProductQUConversionChanged")
	{
		window.location.reload();
	}
});

if (Grocy.EditMode == "create" && GetUriParam("copy-of") != undefined)
{
	Grocy.Api.Get('objects/products/' + GetUriParam("copy-of"),
		function(sourceProduct)
		{
			if (sourceProduct.parent_product_id != null)
			{
				Grocy.Components.ProductPicker.SetId(sourceProduct.parent_product_id);
			}
			if (sourceProduct.description)
			{
				$("#description").summernote("pasteHTML", sourceProduct.description);
			}
			$("#location_id").val(sourceProduct.location_id);
			if (sourceProduct.shopping_location_id != null)
			{
				Grocy.Components.ShoppingLocationPicker.SetId(sourceProduct.shopping_location_id);
			}
			$("#min_stock_amount").val(sourceProduct.min_stock_amount);
			if (BoolVal(sourceProduct.cumulate_min_stock_amount_of_sub_products))
			{
				$("#cumulate_min_stock_amount_of_sub_products").prop("checked", true);
			}
			$("#default_best_before_days").val(sourceProduct.default_best_before_days);
			$("#default_best_before_days_after_open").val(sourceProduct.default_best_before_days_after_open);
			if (sourceProduct.product_group_id != null)
			{
				$("#product_group_id").val(sourceProduct.product_group_id);
			}
			$("#qu_id_stock").val(sourceProduct.qu_id_stock);
			$("#qu_id_purchase").val(sourceProduct.qu_id_purchase);
			if (BoolVal(sourceProduct.enable_tare_weight_handling))
			{
				$("#enable_tare_weight_handling").prop("checked", true);
			}
			$("#tare_weight").val(sourceProduct.tare_weight);
			if (BoolVal(sourceProduct.not_check_stock_fulfillment_for_recipes))
			{
				$("#not_check_stock_fulfillment_for_recipes").prop("checked", true);
			}
			if (sourceProduct.calories != null)
			{
				$("#calories").val(sourceProduct.calories);
			}
			$("#default_best_before_days_after_freezing").val(sourceProduct.default_best_before_days_after_freezing);
			$("#default_best_before_days_after_thawing").val(sourceProduct.default_best_before_days_after_thawing);
			$("#quick_consume_amount").val(sourceProduct.quick_consume_amount);
			$("#quick_open_amount").val(sourceProduct.quick_open_amount);
			$("#default_consume_location_id").val(sourceProduct.default_consume_location_id);
			if (BoolVal(sourceProduct.no_own_stock))
			{
				$("#no_own_stock").prop("checked", true);
			}
			if (BoolVal(sourceProduct.hide_on_stock_overview))
			{
				$("#hide_on_stock_overview").prop("checked", true);
			}
			if (BoolVal(sourceProduct.auto_reprint_stock_label))
			{
				$("#auto_reprint_stock_label").prop("checked", true);
			}
			$("#default_stock_label_type").val(sourceProduct.default_stock_label_type);
			if (BoolVal(sourceProduct.move_on_open))
			{
				$("#move_on_open").prop("checked", true);
			}
			if (BoolVal(sourceProduct.treat_opened_as_out_of_stock))
			{
				$("#treat_opened_as_out_of_stock").prop("checked", true);
			}

			Grocy.FrontendHelpers.ValidateForm('product-form');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}
else if (Grocy.EditMode === 'create')
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

	if (Grocy.UserSettings.product_presets_default_due_days.toString() !== '0')
	{
		$("#default_best_before_days").val(Grocy.UserSettings.product_presets_default_due_days);
	}

	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
	{
		$("#treat_opened_as_out_of_stock").prop("checked", BoolVal(Grocy.UserSettings.product_presets_treat_opened_as_out_of_stock));
	}

	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_LABEL_PRINTER)
	{
		$("#default_stock_label_type").val(Grocy.UserSettings.product_presets_default_stock_label_type);
	}
}

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	var parentProductId = $(e.target).val();

	if (parentProductId)
	{
		Grocy.Api.Get('objects/products/' + parentProductId,
			function(parentProduct)
			{
				if (BoolVal(parentProduct.cumulate_min_stock_amount_of_sub_products))
				{

					$("#min_stock_amount").attr("disabled", "");
				}
				else
				{
					$('#min_stock_amount').removeAttr("disabled");
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		$('#min_stock_amount').removeAttr("disabled");
	}
});

Grocy.FrontendHelpers.ValidateForm("product-form");
Grocy.Components.ProductPicker.GetPicker().trigger("change");

if (Grocy.EditMode == "edit")
{
	$(".save-product-button").toggleClass("default-submit-button");
}
