import BasePicker from "./BasePicker";

class productpicker extends BasePicker
{

	constructor(Grocy, scopeSelector = null)
	{
		super(Grocy, "#product_id", scopeSelector)

		this.Grocy.Use('barcodescanner');

		this.picker = this.$('#product_id');
		this.input_element = this.$('#product_id_text_input');

		var self = this;

		this.embedded = "";
		if (this.Grocy.GetUriParam("embedded") !== undefined)
		{
			this.embedded = "embedded";
		}

		this.initCombobox('.product-combobox');

		this.prefill();

		if (this.Grocy.GetUriParam("flow") === "InplaceAddBarcodeToExistingProduct")
		{
			this.$('#InplaceAddBarcodeToExistingProduct').text(this.Grocy.GetUriParam("barcode"));
			this.$('#flow-info-InplaceAddBarcodeToExistingProduct').removeClass('d-none');
			this.$('#barcode-lookup-disabled-hint').removeClass('d-none');
			this.$('#barcode-lookup-hint').addClass('d-none');
		}

		this.PopupOpen = false;

		this.$('#product_id_text_input').on('blur', (e) => self.onBlurHandler(this, e));
		this.scope.on("Grocy.BarcodeScanned", (e, barcode, target) => self.onBarcodeScannedHandler(e, barcode, target));

		$(document).on("shown.bs.modal", function(e)
		{
			self.$(".modal-footer").addClass("d-block").addClass("d-sm-flex");
			self.$(".modal-footer").find("button").addClass("mt-2").addClass("mt-sm-0");
		});

		// Make that ENTER behaves the same like TAB (trigger blur to start workflows, 
		// but only when the dropdown is not opened)
		this.$('#product_id_text_input').keydown(function(event)
		{
			if (event.keyCode === 13) // Enter
			{
				if (this.picker.hasClass("combobox-menu-visible"))
				{
					return;
				}

				self.$("#product_id_text_input").trigger("blur");
			}
		});
	}

	prefill()
	{
		var doFocus = false;

		var prefillProduct = this.Grocy.GetUriParam('product-name');
		var prefillProduct2 = this.picker.parent().data('prefill-by-name').toString();
		if (!prefillProduct2.isEmpty())
		{
			prefillProduct = prefillProduct2;
		}
		if (typeof prefillProduct !== "undefined")
		{
			var possibleOptionElement = this.$("#product_id option[data-additional-searchdata*=\"" + prefillProduct + "\"]").first();
			if (possibleOptionElement.length === 0)
			{
				possibleOptionElement = this.$("#product_id option:contains(\"" + prefillProduct + "\")").first();
			}

			if (possibleOptionElement.length > 0)
			{
				doFocus = true;
				this.picker.val(possibleOptionElement.val());
			}
		}

		var prefillProductId = this.Grocy.GetUriParam("product");
		var prefillProductId2 = this.picker.parent().data('prefill-by-id').toString();
		if (!prefillProductId2.isEmpty())
		{
			prefillProductId = prefillProductId2;
		}
		if (typeof prefillProductId !== "undefined")
		{
			this.picker.val(prefillProductId);
			doFocus = true;
		}

		if (doFocus)
		{
			this.picker.data('combobox').refresh();
			this.picker.trigger('change');

			this.$(this.picker.parent().data('next-input-selector').toString())
				.focus();
		}
	}

	InProductAddWorkflow()
	{
		return this.Grocy.GetUriParam('flow') == "InplaceNewProductWithName";
	}

	InProductModifyWorkflow()
	{
		return this.Grocy.GetUriParam('flow') == "InplaceAddBarcodeToExistingProduct";
	}

	InAnyFlow()
	{
		return this.InProductAddWorkflow() || this.InProductModifyWorkflow();
	}

	FinishFlow()
	{
		this.Grocy.RemoveUriParam("flow");
		this.Grocy.RemoveUriParam("barcode");
		this.Grocy.RemoveUriParam("product-name");
	}

	ShowCustomError(text)
	{
		var element = this.$("#custom-productpicker-error");
		element.text(text);
		element.removeClass("d-none");
	}

	HideCustomError()
	{
		this.$("#custom-productpicker-error").addClass("d-none");
	}

	Disable()
	{
		this.input_element.attr("disabled", "");
		this.$("#barcodescanner-start-button").attr("disabled", "");
		this.$("#barcodescanner-start-button").addClass("disabled");
	}

	Enable()
	{
		this.input_element.removeAttr("disabled");
		this.$("#barcodescanner-start-button").removeAttr("disabled");
		this.$("#barcodescanner-start-button").removeClass("disabled");
	}

	onBlurHandler(_this, e)
	{
		var self = this;

		if (this.picker.hasClass("combobox-menu-visible"))
		{
			return;
		}

		this.picker.attr("barcode", "null");

		var input = this.$('#product_id_text_input').val().toString();
		var possibleOptionElement = [];

		// did we enter a grocycode?
		if (input.startsWith("grcy"))
		{
			var gc = input.split(":");
			if (gc[1] == "p")
			{
				// find product id
				possibleOptionElement = this.$("#product_id option[value=\"" + gc[2] + "\"]").first();
				this.picker.data("grocycode", true);
			}
		}
		else // process barcode as usual
		{
			possibleOptionElement = this.$("#product_id option[data-additional-searchdata*=\"" + input + ",\"]").first();
		}

		if (this.Grocy.GetUriParam('flow') === undefined && input.length > 0 && possibleOptionElement.length > 0)
		{
			this.picker.val(possibleOptionElement.val());
			this.picker.attr("barcode", input);
			this.picker.data('combobox').refresh();
			this.picker.trigger('change');
		}
		else
		{
			if (this.PopupOpen === true)
			{
				return;
			}

			var optionElement = this.$("#product_id option:contains(\"" + input + "\")").first();
			if (input.length > 0 &&
				optionElement.length === 0 &&
				this.Grocy.GetUriParam('flow') === undefined &&
				this.picker.parent().data('disallow-all-product-workflows').toString() === "false")
			{
				var addProductWorkflowsAdditionalCssClasses = "";
				if (this.picker.parent().data('disallow-add-product-workflows').toString() === "true")
				{
					addProductWorkflowsAdditionalCssClasses = "d-none";
				}

				var buttons = {
					cancel: {
						label: this.Grocy.translate('Cancel'),
						className: 'btn-secondary responsive-button',
						callback: function()
						{
							self.PopupOpen = false;
							self.SetValue('');
						}
					},
					addnewproduct: {
						label: '<strong>P</strong> ' + this.Grocy.translate('Add as new product'),
						className: 'btn-success add-new-product-dialog-button responsive-button ' + addProductWorkflowsAdditionalCssClasses,
						callback: function()
						{

							self.PopupOpen = false;
							window.location.href = self.Grocy.FormatUrl(
								'/product/new?flow=InplaceNewProductWithName' +
								'&name=' + encodeURIComponent(input) +
								'&returnto=' + encodeURIComponent(
									self.Grocy.CurrentUrlRelative +
									"?flow=InplaceNewProductWithName" +
									"&" + self.embedded) +
								"&" + self.embedded);
						}
					},
					addbarcode: {
						label: '<strong>B</strong> ' + self.Grocy.translate('Add as barcode to existing product'),
						className: 'btn-info add-new-barcode-dialog-button responsive-button',
						callback: function()
						{
							self.PopupOpen = false;
							window.location.href = self.Grocy.FormatUrl(
								self.Grocy.CurrentUrlRelative +
								'?flow=InplaceAddBarcodeToExistingProduct' +
								'&barcode=' + encodeURIComponent(input)
							);
						}
					},
					addnewproductwithbarcode: {
						label: '<strong>A</strong> ' + self.Grocy.translate('Add as new product and prefill barcode'),
						className: 'btn-warning add-new-product-with-barcode-dialog-button responsive-button ' + addProductWorkflowsAdditionalCssClasses,
						callback: function()
						{
							self.PopupOpen = false;
							window.location.href = self.Grocy.FormatUrl(
								'/product/new' +
								'?flow=InplaceNewProductWithBarcode' +
								'&barcode=' + encodeURIComponent(input) +
								'&returnto=' + encodeURIComponent(
									self.Grocy.CurrentUrlRelative +
									"?flow=InplaceAddBarcodeToExistingProduct" +
									"&barcode=" + input +
									"&" + self.embedded
								) +
								"&" + self.embedded);
						}
					}
				};

				if (!this.Grocy.FeatureFlags.DISABLE_BROWSER_BARCODE_CAMERA_SCANNING)
				{
					buttons.retrycamerascanning = {
						label: '<strong>C</strong> <i class="fas fa-camera"></i>',
						className: 'btn-primary responsive-button retry-camera-scanning-button',
						callback: function()
						{
							self.PopupOpen = false;
							self.SetValue('');
							self.$("#barcodescanner-start-button").click();
						}
					};
				}

				this.PopupOpen = true;
				bootbox.dialog({
					message: this.Grocy.translate('"%s" could not be resolved to a product, how do you want to proceed?', input),
					title: this.Grocy.translate('Create or assign product'),
					onEscape: function()
					{
						self.PopupOpen = false;
						self.SetValue('');
					},
					size: 'large',
					backdrop: true,
					closeButton: false,
					buttons: buttons
				}).on('keypress', function(e)
				{
					if (e.key === 'B' || e.key === 'b')
					{
						self.$('.add-new-barcode-dialog-button').not(".d-none").click();
					}
					if (e.key === 'p' || e.key === 'P')
					{
						self.$('.add-new-product-dialog-button').not(".d-none").click();
					}
					if (e.key === 'a' || e.key === 'A')
					{
						self.$('.add-new-product-with-barcode-dialog-button').not(".d-none").click();
					}
					if (e.key === 'c' || e.key === 'C')
					{
						self.$('.retry-camera-scanning-button').not(".d-none").click();
					}
				});
			}
		}
	}

	onBarcodeScannedHandler(e, barcode, target)
	{
		var self = this;
		if (!(target == "@productpicker" || target == "undefined" || target == undefined)) // Default target
		{
			return;
		}

		// Don't know why the blur event does not fire immediately ... this works...
		this.input_element
			.focusout()
			.focus()
			.blur();

		this.input_element.val(barcode);

		setTimeout(function()
		{
			self.input_element
				.focusout()
				.focus()
				.blur();
		}, 200);
	}
}

export { productpicker }