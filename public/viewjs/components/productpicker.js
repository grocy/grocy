Grocy.Components.ProductPicker = {};

Grocy.Components.ProductPicker.GetPicker = function()
{
	return $('#product_id');
}

Grocy.Components.ProductPicker.GetInputElement = function()
{
	return $('#product_id_text_input');
}

Grocy.Components.ProductPicker.GetValue = function()
{
	return $('#product_id').val();
}

Grocy.Components.ProductPicker.SetValue = function(value)
{
	Grocy.Components.ProductPicker.GetInputElement().val(value);
	Grocy.Components.ProductPicker.GetInputElement().trigger('change');
}

Grocy.Components.ProductPicker.SetId = function(value)
{
	Grocy.Components.ProductPicker.GetPicker().val(value);
	Grocy.Components.ProductPicker.GetPicker().data('combobox').refresh();
	Grocy.Components.ProductPicker.GetInputElement().trigger('change');
}

Grocy.Components.ProductPicker.Clear = function()
{
	Grocy.Components.ProductPicker.SetValue('');
	Grocy.Components.ProductPicker.SetId(null);
}

Grocy.Components.ProductPicker.InProductAddWorkflow = function()
{
	return typeof GetUriParam('createdproduct') !== "undefined" || typeof GetUriParam('product') !== "undefined";
}

Grocy.Components.ProductPicker.InProductModifyWorkflow = function()
{
	return typeof GetUriParam('addbarcodetoselection') !== "undefined";
}

Grocy.Components.ProductPicker.ShowCustomError = function(text)
{
	var element = $("#custom-productpicker-error");
	element.text(text);
	element.removeClass("d-none");
}

Grocy.Components.ProductPicker.HideCustomError = function()
{
	$("#custom-productpicker-error").addClass("d-none");
}

Grocy.Components.ProductPicker.Disable = function()
{
	Grocy.Components.ProductPicker.GetInputElement().attr("disabled", "");
	$("#barcodescanner-start-button").attr("disabled", "");
	$("#barcodescanner-start-button").addClass("disabled");
}

Grocy.Components.ProductPicker.Enable = function()
{
	Grocy.Components.ProductPicker.GetInputElement().removeAttr("disabled");
	$("#barcodescanner-start-button").removeAttr("disabled");
	$("#barcodescanner-start-button").removeClass("disabled");
}

$('.product-combobox').combobox({
	appendId: '_text_input',
	bsVersion: '4',
	clearIfNoMatch: false
});

var prefillProduct = GetUriParam('createdproduct');
var prefillProduct2 = Grocy.Components.ProductPicker.GetPicker().parent().data('prefill-by-name').toString();
if (!prefillProduct2.isEmpty())
{
	prefillProduct = prefillProduct2;
}
if (typeof prefillProduct !== "undefined")
{
	var possibleOptionElement = $("#product_id option[data-additional-searchdata*=\"" + prefillProduct + "\"]").first();
	if (possibleOptionElement.length === 0)
	{
		possibleOptionElement = $("#product_id option:contains(\"" + prefillProduct + "\")").first();
	}

	if (possibleOptionElement.length > 0)
	{
		$('#product_id').val(possibleOptionElement.val());
		$('#product_id').data('combobox').refresh();
		$('#product_id').trigger('change');

		var nextInputElement = $(Grocy.Components.ProductPicker.GetPicker().parent().data('next-input-selector').toString());
		nextInputElement.focus();
	}
}

var prefillProductId = GetUriParam("product");
var prefillProductId2 = Grocy.Components.ProductPicker.GetPicker().parent().data('prefill-by-id').toString();
if (!prefillProductId2.isEmpty())
{
	prefillProductId = prefillProductId2;
}
if (typeof prefillProductId !== "undefined")
{
	$('#product_id').val(prefillProductId);
	$('#product_id').data('combobox').refresh();
	$('#product_id').trigger('change');

	var nextInputElement = $(Grocy.Components.ProductPicker.GetPicker().parent().data('next-input-selector').toString());
	nextInputElement.focus();
}

var addBarcode = GetUriParam('addbarcodetoselection');
if (addBarcode !== undefined)
{
	$('#addbarcodetoselection').text(addBarcode);
	$('#flow-info-addbarcodetoselection').removeClass('d-none');
	$('#barcode-lookup-disabled-hint').removeClass('d-none');
}

Grocy.Components.ProductPicker.PopupOpen = false;
$('#product_id_text_input').on('blur', function(e)
{
	if (Grocy.Components.ProductPicker.GetPicker().hasClass("combobox-menu-visible"))
	{
		return;
	}
	$('#product_id').attr("barcode", "null");

	var input = $('#product_id_text_input').val().toString();
	var possibleOptionElement = $("#product_id option[data-additional-searchdata*=\"" + input + ",\"]").first();

	if (GetUriParam('addbarcodetoselection') === undefined && input.length > 0 && possibleOptionElement.length > 0)
	{
		$('#product_id').val(possibleOptionElement.val());
		$('#product_id').attr("barcode", input);
		$('#product_id').data('combobox').refresh();
		$('#product_id').trigger('change');
	}
	else
	{
		if (Grocy.Components.ProductPicker.PopupOpen === true)
		{
			return;
		}

		var optionElement = $("#product_id option:contains(\"" + input + "\")").first();
		if (input.length > 0 && optionElement.length === 0 && typeof GetUriParam('addbarcodetoselection') === "undefined" && Grocy.Components.ProductPicker.GetPicker().parent().data('disallow-all-product-workflows').toString() === "false")
		{
			var addProductWorkflowsAdditionalCssClasses = "";
			if (Grocy.Components.ProductPicker.GetPicker().parent().data('disallow-add-product-workflows').toString() === "true")
			{
				addProductWorkflowsAdditionalCssClasses = "d-none";
			}

			Grocy.Components.ProductPicker.PopupOpen = true;
			bootbox.dialog({
				message: __t('"%s" could not be resolved to a product, how do you want to proceed?', input),
				title: __t('Create or assign product'),
				onEscape: function()
				{
					Grocy.Components.ProductPicker.PopupOpen = false;
					Grocy.Components.ProductPicker.SetValue('');
				},
				size: 'large',
				backdrop: true,
				closeButton: false,
				buttons: {
					cancel: {
						label: __t('Cancel'),
						className: 'btn-secondary responsive-button',
						callback: function()
						{
							Grocy.Components.ProductPicker.PopupOpen = false;
							Grocy.Components.ProductPicker.SetValue('');
						}
					},
					addnewproduct: {
						label: '<strong>P</strong> ' + __t('Add as new product'),
						className: 'btn-success add-new-product-dialog-button responsive-button ' + addProductWorkflowsAdditionalCssClasses,
						callback: function()
						{
							Grocy.Components.ProductPicker.PopupOpen = false;
							window.location.href = U('/product/new?prefillname=' + encodeURIComponent(input) + '&returnto=' + encodeURIComponent(Grocy.CurrentUrlRelative));
						}
					},
					addbarcode: {
						label: '<strong>B</strong> ' + __t('Add as barcode to existing product'),
						className: 'btn-info add-new-barcode-dialog-button responsive-button',
						callback: function()
						{
							Grocy.Components.ProductPicker.PopupOpen = false;
							window.location.href = U(Grocy.CurrentUrlRelative + '?addbarcodetoselection=' + encodeURIComponent(input));
						}
					},
					addnewproductwithbarcode: {
						label: '<strong>A</strong> ' + __t('Add as new product and prefill barcode'),
						className: 'btn-warning add-new-product-with-barcode-dialog-button responsive-button ' + addProductWorkflowsAdditionalCssClasses,
						callback: function()
						{
							Grocy.Components.ProductPicker.PopupOpen = false;
							window.location.href = U('/product/new?prefillbarcode=' + encodeURIComponent(input) + '&returnto=' + encodeURIComponent(Grocy.CurrentUrlRelative));
						}
					}
				}
			}).on('keypress', function(e)
			{
				if (e.key === 'B' || e.key === 'b')
				{
					$('.add-new-barcode-dialog-button').not(".d-none").click();
				}
				if (e.key === 'p' || e.key === 'P')
				{
					$('.add-new-product-dialog-button').not(".d-none").click();
				}
				if (e.key === 'a' || e.key === 'A')
				{
					$('.add-new-product-with-barcode-dialog-button').not(".d-none").click();
				}
			});
		}
	}
});

$(document).on("Grocy.BarcodeScanned", function(e, barcode, target)
{
	if (!(target == "@productpicker" || target == "undefined" || target == undefined)) // Default target
	{
		return;
	}

	// Don't know why the blur event does not fire immediately ... this works...

	Grocy.Components.ProductPicker.GetInputElement().focusout();
	Grocy.Components.ProductPicker.GetInputElement().focus();
	Grocy.Components.ProductPicker.GetInputElement().blur();

	Grocy.Components.ProductPicker.GetInputElement().val(barcode);

	setTimeout(function()
	{
		Grocy.Components.ProductPicker.GetInputElement().focusout();
		Grocy.Components.ProductPicker.GetInputElement().focus();
		Grocy.Components.ProductPicker.GetInputElement().blur();
	}, 200);
});

$(document).on("shown.bs.modal", function(e)
{
	$(".modal-footer").addClass("d-block").addClass("d-sm-flex");
	$(".modal-footer").find("button").addClass("mt-2").addClass("mt-sm-0");
})
