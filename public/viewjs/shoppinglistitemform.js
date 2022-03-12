Grocy.ShoppingListItemFormInitialLoadDone = false;

$('#save-shoppinglist-button').on('click', function(e) {
	e.preventDefault();

	if ($(".combobox-menu-visible").length) {
		return;
	}

	var jsonData = $('#shoppinglist-form').serializeJSON();
	var displayAmount = parseFloat(jsonData.display_amount);
	if (!jsonData.product_id) {
		jsonData.amount = jsonData.display_amount;
	}
	delete jsonData.display_amount;

	Grocy.FrontendHelpers.BeginUiBusy("shoppinglist-form");

	if (GetUriParam("flow") === "InplaceAddBarcodeToExistingProduct") {
		var jsonDataBarcode = {};
		jsonDataBarcode.barcode = GetUriParam("barcode");
		jsonDataBarcode.product_id = jsonData.product_id;

		Grocy.Api.Post('objects/product_barcodes', jsonDataBarcode,
			function(result) {
				$("#flow-info-InplaceAddBarcodeToExistingProduct").addClass("d-none");
				$('#barcode-lookup-disabled-hint').addClass('d-none');
				$('#barcode-lookup-hint').removeClass('d-none');
			},
			function(xhr) {
				Grocy.FrontendHelpers.EndUiBusy("shoppinglist-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}

	if (GetUriParam("updateexistingproduct") !== undefined) {
		jsonData.product_amount = jsonData.amount;
		delete jsonData.amount;

		jsonData.list_id = jsonData.shopping_list_id;
		delete jsonData.shopping_list_id;

		Grocy.Api.Post('stock/shoppinglist/add-product', jsonData,
			function(result) {
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save();

				if (GetUriParam("embedded") !== undefined) {
					Grocy.Api.Get('stock/products/' + jsonData.product_id,
						function(productDetails) {
							window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", displayAmount.toLocaleString({
								minimumFractionDigits: 0,
								maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts
							}) + " " + __n(displayAmount, $("#qu_id option:selected").text(), $("#qu_id option:selected").attr("data-qu-name-plural"), true), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
							window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
						},
						function(xhr) {
							console.error(xhr);
						}
					);
				} else {
					window.location.href = U('/shoppinglist?list=' + $("#shopping_list_id").val().toString());
				}
			},
			function(xhr) {
				Grocy.FrontendHelpers.EndUiBusy("shoppinglist-form");
				console.error(xhr);
			}
		);
	} else if (Grocy.EditMode === 'create') {
		Grocy.Api.Post('objects/shopping_list', jsonData,
			function(result) {
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save();

				if (GetUriParam("embedded") !== undefined) {
					if (jsonData.product_id) {
						Grocy.Api.Get('stock/products/' + jsonData.product_id,
							function(productDetails) {
								window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", displayAmount.toLocaleString({
									minimumFractionDigits: 0,
									maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts
								}) + " " + __n(displayAmount, $("#qu_id option:selected").text(), $("#qu_id option:selected").attr("data-qu-name-plural"), true), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
							},
							function(xhr) {
								console.error(xhr);
							}
						);
					} else {
						window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					}
				} else {
					window.location.href = U('/shoppinglist?list=' + $("#shopping_list_id").val().toString());
				}
			},
			function(xhr) {
				Grocy.FrontendHelpers.EndUiBusy("shoppinglist-form");
				console.error(xhr);
			}
		);
	} else {
		Grocy.Api.Put('objects/shopping_list/' + Grocy.EditObjectId, jsonData,
			function(result) {
				Grocy.Components.UserfieldsForm.Save();

				if (GetUriParam("embedded") !== undefined) {
					if (jsonData.product_id) {
						Grocy.Api.Get('stock/products/' + jsonData.product_id,
							function(productDetails) {
								window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", __t("Added %1$s of %2$s to the shopping list \"%3$s\"", displayAmount.toLocaleString({
									minimumFractionDigits: 0,
									maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts
								}) + " " + __n(displayAmount, $("#qu_id option:selected").text(), $("#qu_id option:selected").attr("data-qu-name-plural"), true), productDetails.product.name, $("#shopping_list_id option:selected").text())), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
								window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
							},
							function(xhr) {
								console.error(xhr);
							}
						);
					} else {
						window.parent.postMessage(WindowMessageBag("ShoppingListChanged", $("#shopping_list_id").val().toString()), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					}
				} else {
					window.location.href = U('/shoppinglist?list=' + $("#shopping_list_id").val().toString());
				}
			},
			function(xhr) {
				Grocy.FrontendHelpers.EndUiBusy("shoppinglist-form");
				console.error(xhr);
			}
		);
	}
});

Grocy.Components.ProductPicker.OnChange(function(e) {
	var productId = $(e.target).val();

	if (productId) {
		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails) {
				if (!Grocy.ShoppingListItemFormInitialLoadDone) {
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id, true);
				} else {
					Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);
					Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.default_quantity_unit_purchase.id);
				}

				if ($("#display_amount").val().toString().isEmpty()) {
					$("#display_amount").val(1);
					$("#display_amount").trigger("change");
				}

				$('#display_amount').trigger('focus');
				Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
				Grocy.ShoppingListItemFormInitialLoadDone = true;
			},
			function(xhr) {
				console.error(xhr);
			}
		);
	}

	// TODO: what is the point of this?
	$("#note").trigger("input");
	// Grocy.Components.ProductPicker.GetPicker().trigger("input");
});

Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
setTimeout(function() {
	Grocy.Components.ProductPicker.Focus();
}, 250);

if (Grocy.EditMode == "create") {
	Grocy.ShoppingListItemFormInitialLoadDone = true;
}

$('#display_amount').on('focus', function(e) {
	$(this).trigger('select');
});

$('#shoppinglist-form input').on('keyup', function(event) {
	Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
});

$('#shoppinglist-form input').on('keydown', function(event) {
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('shoppinglist-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		} else {
			$('#save-shoppinglist-button').click();
		}
	}
});

if (GetUriParam("list") !== undefined) {
	$("#shopping_list_id").val(GetUriParam("list"));
}

if (GetUriParam("amount") !== undefined) {
	$("#display_amount").val(parseFloat(GetUriParam("amount")));
	RefreshLocaleNumberInput();
	$(".input-group-productamountpicker").trigger("change");
	Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
}

if (GetUriParam("embedded") !== undefined) {
	if (GetUriParam("product") !== undefined) {
		$("#display_amount").trigger('focus');
	} else {
		Grocy.Components.ProductPicker.Focus();
	}
}

$("#note").prop('required', "");
$("#note").on('input', function() {
	if (!$(this).val().length) {
		Grocy.Components.ProductPicker.Require();
	} else {
		Grocy.Components.ProductPicker.Optional();
	}
	Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
});

Grocy.Components.ProductPicker.OnChange(function() {
	$("#note").prop('required', !$(this).val().length);
	Grocy.FrontendHelpers.ValidateForm('shoppinglist-form');
});

if (GetUriParam("product-name") != null) {
	Grocy.Components.ProductPicker.Validate();
}

Grocy.Components.UserfieldsForm.Load();
