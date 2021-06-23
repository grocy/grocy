import { BoolVal } from '../helpers/extensions';

function productformView(Grocy, scope = null)
{
	var $scope = $;
	var top = scope != null ? $(scope) : $(document);

	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");
	var shoppinglocationpicker = Grocy.Use("shoppinglocationpicker");
	var userfields = Grocy.Use("userfieldsform");
	var productpicker = Grocy.Use("productpicker");

	function saveProductPicture(result, location, jsonData)
	{
		var productId = Grocy.EditObjectId || result.created_object_id;

		userfields.Save(() =>
		{
			if (Object.prototype.hasOwnProperty.call(jsonData, "picture_file_name") && !Grocy.DeleteProductPictureOnSave)
			{
				Grocy.Api.UploadFile($scope("#product-picture")[0].files[0], 'productpictures', jsonData.picture_file_name,
					(result) =>
					{
						if (Grocy.ProductEditFormRedirectUri == "reload")
						{
							window.location.reload();
							return
						}

						var returnTo = Grocy.GetUriParam('returnto');
						if (Grocy.GetUriParam("closeAfterCreation") !== undefined)
						{
							window.close();
						}
						else if (returnTo !== undefined)
						{
							if (Grocy.GetUriParam("flow") !== undefined)
							{
								window.location.href = U(returnTo) + '&product-name=' + encodeURIComponent($scope('#name').val());
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
						Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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

				var returnTo = Grocy.GetUriParam('returnto');
				if (Grocy.GetUriParam("closeAfterCreation") !== undefined)
				{
					window.close();
				}
				else if (returnTo !== undefined)
				{
					if (Grocy.GetUriParam("flow") !== undefined)
					{
						window.location.href = U(returnTo) + '&product-name=' + encodeURIComponent($scope('#name').val());
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

	$scope('.save-product-button').on('click', function(e)
	{
		e.preventDefault();

		var jsonData = $scope('#product-form').serializeJSON();
		var parentProductId = jsonData.product_id;
		delete jsonData.product_id;
		jsonData.parent_product_id = parentProductId;
		Grocy.FrontendHelpers.BeginUiBusy("product-form");

		if (jsonData.parent_product_id.toString().isEmpty())
		{
			jsonData.parent_product_id = null;
		}

		if ($scope("#product-picture")[0].files.length > 0)
		{
			var someRandomStuff = Math.random().toString(36).substring(2, 100) + Math.random().toString(36).substring(2, 100);
			jsonData.picture_file_name = someRandomStuff + $scope("#product-picture")[0].files[0].name;
		}

		const location = $scope(e.currentTarget).attr('data-location') == 'return' ? '/products?product=' : '/product/';

		if (Grocy.EditMode == 'create')
		{
			Grocy.Api.Post('objects/products', jsonData,
				(result) => saveProductPicture(result, location, jsonData),
				(xhr) =>
				{
					Grocy.FrontendHelpers.EndUiBusy("product-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				});
			return;
		}

		if (Grocy.DeleteProductPictureOnSave)
		{
			jsonData.picture_file_name = null;

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

	if (Grocy.EditMode == "edit")
	{
		Grocy.Api.Get('stock/products/' + Grocy.EditObjectId,
			function(productDetails)
			{
				if (productDetails.last_purchased == null)
				{
					$scope('#qu_id_stock').removeAttr("disabled");
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	if (Grocy.GetUriParam("flow") == "InplaceNewProductWithName")
	{
		$scope('#name').val(Grocy.GetUriParam("name"));
		$scope('#name').focus();
	}

	if (Grocy.GetUriParam("flow") !== undefined || Grocy.GetUriParam("returnto") !== undefined)
	{
		$scope("#save-hint").addClass("d-none");
		$scope(".save-product-button[data-location='return']").addClass("d-none");
	}

	$scope('.input-group-qu').on('change', function(e)
	{
		var quIdPurchase = $scope("#qu_id_purchase").val();
		var quIdStock = $scope("#qu_id_stock").val();
		var factor = $scope('#qu_factor_purchase_to_stock').val();

		if (factor > 1 || quIdPurchase != quIdStock)
		{
			$scope('#qu-conversion-info').text(__t('This means 1 %1$s purchased will be converted into %2$s %3$s in stock', $scope("#qu_id_purchase option:selected").text(), (1 * factor).toString(), __n((1 * factor).toString(), $scope("#qu_id_stock option:selected").text(), $scope("#qu_id_stock option:selected").data("plural-form"))));
			$scope('#qu-conversion-info').removeClass('d-none');
		}
		else
		{
			$scope('#qu-conversion-info').addClass('d-none');
		}

		$scope("#tare_weight_qu_info").text($scope("#qu_id_stock option:selected").text());
		$scope("#quick_consume_qu_info").text($scope("#qu_id_stock option:selected").text());

		Grocy.FrontendHelpers.ValidateForm('product-form');
	});

	$scope('#product-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('product-form');
		$scope(".input-group-qu").trigger("change");
		$scope("#product-form select").trigger("select");

		if (document.getElementById('product-form').checkValidity() === false) //There is at least one validation error
		{
			$scope("#qu-conversion-add-button").addClass("disabled");
		}
		else
		{
			$scope("#qu-conversion-add-button").removeClass("disabled");
		}

		if (document.getElementById('product-form').checkValidity() === false) //There is at least one validation error
		{
			$scope("#barcode-add-button").addClass("disabled");
		}
	});

	$scope('#location_id').change(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('product-form');
	});

	$scope('#product-form input').keydown(function(event)
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
				$scope('#save-product-button').click();
			}
		}
	});

	$scope("#enable_tare_weight_handling").on("click", function()
	{
		if (this.checked)
		{
			$scope("#tare_weight").removeAttr("disabled");
		}
		else
		{
			$scope("#tare_weight").attr("disabled", "");
		}

		Grocy.FrontendHelpers.ValidateForm("product-form");
	});

	$scope("#product-picture").on("change", function(e)
	{
		$scope("#product-picture-label").removeClass("d-none");
		$scope("#product-picture-label-none").addClass("d-none");
		$scope("#delete-current-product-picture-on-save-hint").addClass("d-none");
		$scope("#current-product-picture").addClass("d-none");
		Grocy.DeleteProductPictureOnSave = false;
	});

	Grocy.DeleteProductPictureOnSave = false;
	$scope("#delete-current-product-picture-button").on("click", function(e)
	{
		Grocy.DeleteProductPictureOnSave = true;
		$scope("#current-product-picture").addClass("d-none");
		$scope("#delete-current-product-picture-on-save-hint").removeClass("d-none");
		$scope("#product-picture-label").addClass("d-none");
		$scope("#product-picture-label-none").removeClass("d-none");
	});

	var quConversionsTable = $scope('#qu-conversions-table-products').DataTable({
		'order': [[1, 'asc']],
		"orderFixed": [[4, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 },
			{ 'visible': false, 'targets': 4 }
		].concat($.fn.dataTable.defaults.columnDefs),
		'rowGroup': {
			enable: true,
			dataSrc: 4
		}
	});
	$scope('#qu-conversions-table-products tbody').removeClass("d-none");
	quConversionsTable.columns.adjust().draw();

	var barcodeTable = $scope('#barcode-table').DataTable({
		'order': [[1, 'asc']],
		"orderFixed": [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 },
			{ 'visible': false, 'targets': 5 },
			{ 'visible': false, 'targets': 6 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#barcode-table tbody').removeClass("d-none");
	barcodeTable.columns.adjust().draw();

	userfields.Load();
	$scope("#name").trigger("keyup");
	$scope('#name').focus();
	$scope('.input-group-qu').trigger('change');
	Grocy.FrontendHelpers.ValidateForm('product-form');

	top.on('click', '.stockentry-grocycode-product-label-print', function(e)
	{
		e.preventDefault();
		document.activeElement.blur();

		var productId = $(e.currentTarget).attr('data-product-id');
		Grocy.Api.Get('stock/products/' + productId + '/printlabel', function(labelData)
		{
			if (Grocy.Webhooks.labelprinter !== undefined)
			{
				Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, labelData);
			}
		});
	});

	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to remove this conversion?',
		'.qu-conversion-delete-button',
		'data-qu-conversion-id',
		'data-qu-conversion-id',
		'objects/quantity_unit_conversions/',
		(result, id, name) =>
		{
			Grocy.ProductEditFormRedirectUri = "reload";
			$scope('#save-product-button').click();
		}
	);

	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to remove this barcode?',
		'.barcode-delete-button',
		'data-barcode-id',
		'data-barcode-id',
		'objects/product_barcodes/',
		(result, id, name) =>
		{
			Grocy.ProductEditFormRedirectUri = "reload";
			$scope('#save-product-button').click();
		}
	)

	$scope('#qu_id_stock').change(function(e)
	{
		// Preset QU purchase with stock QU if unset
		var quIdStock = $scope('#qu_id_stock');
		var quIdPurchase = $scope('#qu_id_purchase');

		if (quIdPurchase[0].selectedIndex === 0 && quIdStock[0].selectedIndex !== 0)
		{
			quIdPurchase[0].selectedIndex = quIdStock[0].selectedIndex;
			Grocy.FrontendHelpers.ValidateForm('product-form');
		}
	});

	$scope('#allow_label_per_unit').on('change', function()
	{
		if (this.checked)
		{
			$scope('#label-option-per-unit').prop("disabled", false);
		}
		else
		{
			if ($scope('#default_print_stock_label').val() == "2")
			{
				$scope("#default_print_stock_label").val("0");
			}
			$scope('#label-option-per-unit').prop("disabled", true);
		}
	});

	$(window).on("message", function(e)
	{
		var data = e.originalEvent.data;

		if (data.Message === "ProductBarcodesChanged" || data.Message === "ProductQUConversionChanged")
		{
			window.location.reload();
		}
	});

	if (Grocy.EditMode == "create" && Grocy.GetUriParam("copy-of") != undefined)
	{
		Grocy.Api.Get('objects/products/' + Grocy.GetUriParam("copy-of"),
			function(sourceProduct)
			{
				if (sourceProduct.parent_product_id != null)
				{
					productpicker.SetId(sourceProduct.parent_product_id);
				}
				if (sourceProduct.description != null)
				{
					$scope("#description").summernote("pasteHTML", sourceProduct.description);
				}
				$scope("#location_id").val(sourceProduct.location_id);
				if (sourceProduct.shopping_location_id != null)
				{
					shoppinglocationpicker.SetId(sourceProduct.shopping_location_id);
				}
				$scope("#min_stock_amount").val(sourceProduct.min_stock_amount);
				if (BoolVal(sourceProduct.cumulate_min_stock_amount_of_sub_products))
				{
					$scope("#cumulate_min_stock_amount_of_sub_products").prop("checked", true);
				}
				$scope("#default_best_before_days").val(sourceProduct.default_best_before_days);
				$scope("#default_best_before_days_after_open").val(sourceProduct.default_best_before_days_after_open);
				if (sourceProduct.product_group_id != null)
				{
					$scope("#product_group_id").val(sourceProduct.product_group_id);
				}
				$scope("#qu_id_stock").val(sourceProduct.qu_id_stock);
				$scope("#qu_id_purchase").val(sourceProduct.qu_id_purchase);
				$scope("#qu_factor_purchase_to_stock").val(sourceProduct.qu_factor_purchase_to_stock);
				if (BoolVal(sourceProduct.enable_tare_weight_handling))
				{
					$scope("#enable_tare_weight_handling").prop("checked", true);
				}
				$scope("#tare_weight").val(sourceProduct.tare_weight);
				if (BoolVal(sourceProduct.not_check_stock_fulfillment_for_recipes))
				{
					$scope("#not_check_stock_fulfillment_for_recipes").prop("checked", true);
				}
				if (sourceProduct.calories != null)
				{
					$scope("#calories").val(sourceProduct.calories);
				}
				$scope("#default_best_before_days_after_freezing").val(sourceProduct.default_best_before_days_after_freezing);
				$scope("#default_best_before_days_after_thawing").val(sourceProduct.default_best_before_days_after_thawing);
				$scope("#quick_consume_amount").val(sourceProduct.quick_consume_amount);

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
			$scope("#location_id").val(Grocy.UserSettings.product_presets_location_id);
		}

		if (Grocy.UserSettings.product_presets_product_group_id.toString() !== '-1')
		{
			$scope("#product_group_id").val(Grocy.UserSettings.product_presets_product_group_id);
		}

		if (Grocy.UserSettings.product_presets_qu_id.toString() !== '-1')
		{
			$scope("select.input-group-qu").val(Grocy.UserSettings.product_presets_qu_id);
		}
	}

	Grocy.FrontendHelpers.ValidateForm("product-form");

}



window.productformView = productformView
