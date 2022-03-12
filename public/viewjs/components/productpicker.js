Grocy.Components.ProductPicker = {};

Grocy.Components.ProductPicker.GetPicker = function() {
	return $('#product_id');
}

Grocy.Components.ProductPicker.GetValue = function() {
	return this.GetPicker().val();
}

Grocy.Components.ProductPicker.GetOption = function(key) {
	return this.GetPicker().parents('.form-group').data(key);
}

Grocy.Components.ProductPicker.GetState = function(key) {
	return this.GetPicker().data(key);
}

Grocy.Components.ProductPicker.SetState = function(key, value) {
	this.GetPicker().data(key, value);
	return this;
}

Grocy.Components.ProductPicker.SetId = function(value, callback) {
	if (this.GetPicker().find('option[value="' + value + '"]').length) {
		this.GetPicker().val(value).trigger('change');
	} else {
		Grocy.Api.Get('objects/products/' + encodeURIComponent(value),
			function(result) {
				var option = new Option(result.name, value, true, true);
				if (typeof callback === 'function') {
					Grocy.Components.ProductPicker.GetPicker().one('change.select2', callback);
				}
				Grocy.Components.ProductPicker.GetPicker().append(option).trigger('change').select2('close');
			},
			function(xhr) {
				console.error(xhr);
			}
		);
	}
	return this;
}

Grocy.Components.ProductPicker.Clear = function() {
	this.GetPicker().val(null).trigger('change');
	return this;
}

Grocy.Components.ProductPicker.InProductAddWorkflow = function() {
	return GetUriParam('flow') == "InplaceNewProductWithName";
}

Grocy.Components.ProductPicker.InProductModifyWorkflow = function() {
	return GetUriParam('flow') == "InplaceAddBarcodeToExistingProduct";
}

Grocy.Components.ProductPicker.InAnyFlow = function() {
	return GetUriParam('flow') !== undefined;
}

Grocy.Components.ProductPicker.FinishFlow = function() {
	RemoveUriParam("flow");
	RemoveUriParam("barcode");
	RemoveUriParam("product-name");
	return this;
}

Grocy.Components.ProductPicker.ShowCustomError = function(text) {
	var element = $("#custom-productpicker-error");
	element.text(text);
	element.removeClass("d-none");
	return this;
}

Grocy.Components.ProductPicker.HideCustomError = function() {
	$("#custom-productpicker-error").addClass("d-none");
	return this;
}

Grocy.Components.ProductPicker.Disable = function() {
	this.GetPicker().prop("disabled", true);
	$("#barcodescanner-start-button").attr("disabled", "");
	$("#barcodescanner-start-button").addClass("disabled");
	return this;
}

Grocy.Components.ProductPicker.Enable = function() {
	this.GetPicker().prop("disabled", false);
	$("#barcodescanner-start-button").removeAttr("disabled");
	$("#barcodescanner-start-button").removeClass("disabled");
	return this;
}

Grocy.Components.ProductPicker.IsRequired = function() {
	return this.GetPicker().prop("required");
}

Grocy.Components.ProductPicker.Require = function() {
	this.GetPicker().prop("required", true);
	return this;
}

Grocy.Components.ProductPicker.Optional = function() {
	this.GetPicker().prop("required", false);
	return this;
}

Grocy.Components.ProductPicker.Focus = function() {
	this.GetPicker().select2('open');
	return this;
}

Grocy.Components.ProductPicker.Validate = function() {
	this.GetPicker().trigger('change');
	return this;
}

Grocy.Components.ProductPicker.OnChange = function(eventHandler) {
	this.GetPicker().on('change', eventHandler);
	return this;
}

Grocy.Components.ProductPicker.Search = function(term, callback) {
	var $picker = this.GetPicker();
	var doSearch = function() {
		var $search = $picker.data('select2').dropdown.$search || $picker.data('select2').selection.$search;
		$search.val(term).trigger('input');
		// must wait for debounce before listening for 'results:all' event
		setTimeout(function() {
			$picker.one('Grocy.ResultsUpdated', function() {
				if (typeof callback === 'function') {
					$picker.one('select2:close', callback);
				}
				$picker.select2('close');
			});
		}, 150);
	};
	if ($picker.data('select2').isOpen()) {
		doSearch();
	} else {
		$picker.one('select2:open', doSearch);
		$picker.select2('open');
	}
	return this;
}

Grocy.Components.ProductPicker.IsOpen = function() {
	return this.GetPicker().parent().find('.select2-container').hasClass('select2-container--open');
}

// initialize Select2 product picker
var lastProductSearchTerm = '';
Grocy.Components.ProductPicker.GetPicker().select2({
	placeholder: Grocy.Components.ProductPicker.IsRequired() ? null : __t('All'),
	placeholderOption: 'all',
	selectOnClose: Grocy.Components.ProductPicker.IsRequired(),
	allowClear: !Grocy.Components.ProductPicker.IsRequired(),
	ajax: {
		delay: 150,
		transport: function(params, success, failure) {
			var results_per_page = 10;
			var page = params.data.page || 1;
			var term = params.data.term || "";
			var termIsGrocycode = term.startsWith("grcy");
			lastProductSearchTerm = term;

			// reset grocycode/barcode state
			Grocy.Components.ProductPicker.SetState('barcode', 'null');
			Grocy.Components.ProductPicker.SetState('grocycode', false);

			// build search queries
			var baseQuery = Grocy.Components.ProductPicker.GetOption('products-query').split('&');
			baseQuery.push('limit=' + encodeURIComponent(results_per_page));
			baseQuery.push('offset=' + encodeURIComponent((page - 1) * results_per_page));
			var queries = [];
			if (term.length > 0) {
				queries = [
					// search product fields (name, etc.)
					baseQuery.concat('search=' + encodeURIComponent(term)).join('&'),
				];
				// grocycode handling
				if (termIsGrocycode) {
					var gc = term.split(":");
					if (gc[1] == "p") {
						queries.push(baseQuery.concat('query%5B%5D=id%3D' + encodeURIComponent(gc[2])).join('&'));
					}
				}
			} else {
				queries = [baseQuery.join('&')];
			}

			// execute all queries in parallel, return first non-empty response
			var complete = 0;
			var responded = false;
			var xhrs = [];
			var handleEmptyResponse = function() {
				if (responded || complete < xhrs.length) return;
				success({
					results: Grocy.Components.ProductPicker.IsRequired() ? [] : [{
						id: 'all',
						text: __t('All'),
					}],
					pagination: {
						more: false
					}
				});
			};
			var handleResponse = function(results, meta, cur_xhr) {
				// track complete queries
				complete++;

				// abort if we already responded
				if (responded) return;

				// abort if no results
				if (results.length === 0) {
					handleEmptyResponse();
					return;
				}

				// track whether we have responded
				responded = true;

				// abort all other queries
				xhrs.forEach(function(xhr) {
					if (xhr !== cur_xhr) xhr.abort();
				});

				// update grocycode/barcode state
				Grocy.Components.ProductPicker.SetState('grocycode', termIsGrocycode);
				Grocy.Components.ProductPicker.SetState('barcode', term);

				success({
					results: (Grocy.Components.ProductPicker.IsRequired() ? [] : [{
						id: 'all',
						text: __t('All'),
					}]).concat(results.map(function(result) {
						return {
							id: result.id,
							text: result.name
						};
					})),
					pagination: {
						more: page * results_per_page < meta.recordsFiltered
					}
				});
			};
			var handleErrors = function(xhr) {
				console.error(xhr);

				// track complete queries
				complete++;

				// abort if we already responded
				if (responded) return;

				handleEmptyResponse();
			};
			if (term.length > 0) {
				xhrs.push(Grocy.Api.Get('objects/product_barcodes?limit=1&query%5B%5D=barcode%3D' + encodeURIComponent(term),
					function(results, meta) {
						// track complete queries
						complete++;

						// abort if we already responded
						if (responded) return;

						// abort if no results
						if (results.length === 0) {
							handleEmptyResponse();
							return;
						}

						var cur_xhr = Grocy.Api.Get('objects/products?' + baseQuery.concat('query%5B%5D=id%3D' + encodeURIComponent(results[0].product_id)).join('&'),
							function(results, meta) {
								handleResponse(results, meta, cur_xhr);
							},
							handleErrors
						);
						xhrs.push(cur_xhr);
					},
					handleErrors
				));
			}
			xhrs = xhrs.concat(queries.map(function(query) {
				var cur_xhr = Grocy.Api.Get('objects/products' + (query.length > 0 ? '?' + query : ''),
					function(results, meta) {
						handleResponse(results, meta, cur_xhr);
					},
					handleErrors
				);
				return cur_xhr;
			}));
		}
	}
});

// forward 'results:all' event
Grocy.Components.ProductPicker.GetPicker().data('select2').on('results:all', function(data) {
	Grocy.Components.ProductPicker.GetPicker().trigger('Grocy.ResultsUpdated', data);
});

// handle barcode scanning
$(document).on("Grocy.BarcodeScanned", function(e, barcode, target) {
	// check that the barcode scan is for the product picker
	if (!(target == "@productpicker" || target == "undefined" || target == undefined)) return;

	Grocy.Components.ProductPicker.Search(barcode);
});

// fix placement of bootbox footer buttons
$(document).on("shown.bs.modal", function() {
	$(".modal-footer").addClass("d-block").addClass("d-sm-flex");
	$(".modal-footer").find("button").addClass("mt-2").addClass("mt-sm-0");
});

if (GetUriParam("flow") === "InplaceAddBarcodeToExistingProduct") {
	$('#InplaceAddBarcodeToExistingProduct').text(GetUriParam("barcode"));
	$('#flow-info-InplaceAddBarcodeToExistingProduct').removeClass('d-none');
	$('#barcode-lookup-disabled-hint').removeClass('d-none');
	$('#barcode-lookup-hint').addClass('d-none');
}

// prefill by name
var prefillProduct = Grocy.Components.ProductPicker.GetOption('prefill-by-name') || GetUriParam('product-name');
if (typeof prefillProduct === 'string' && !prefillProduct.isEmpty()) {
	Grocy.Components.ProductPicker.Search(prefillProduct, function() {
		$(Grocy.Components.ProductPicker.GetOption('next-input-selector')).trigger('focus');
	});
}

// prefill by ID
var prefillProduct = Grocy.Components.ProductPicker.GetOption('prefill-by-id') || GetUriParam('product');
if (typeof prefillProduct === 'string' && !prefillProduct.isEmpty()) {
	Grocy.Components.ProductPicker.SetId(prefillProduct, function() {
		$(Grocy.Components.ProductPicker.GetOption('next-input-selector')).trigger('focus');
	});
}

// open create product/barcode dialog if no results
Grocy.Components.ProductPicker.PopupOpen = false;
Grocy.Components.ProductPicker.GetPicker().on('select2:close', function() {
	if (Grocy.Components.ProductPicker.PopupOpen || Grocy.Components.ProductPicker.GetPicker().select2('data').length > 0) return;

	var addProductWorkflowsAdditionalCssClasses = "";
	if (Grocy.Components.ProductPicker.GetOption('disallow-add-product-workflows')) {
		addProductWorkflowsAdditionalCssClasses = "d-none";
	}

	var embedded = "";
	if (GetUriParam("embedded") !== undefined) {
		embedded = "embedded";
	}

	var buttons = {
		cancel: {
			label: __t('Cancel'),
			className: 'btn-secondary responsive-button',
			callback: function() {
				Grocy.Components.ProductPicker.PopupOpen = false;
				Grocy.Components.ProductPicker.Clear();
			}
		},
		addnewproduct: {
			label: '<strong>P</strong> ' + __t('Add as new product'),
			className: 'btn-success add-new-product-dialog-button responsive-button ' + addProductWorkflowsAdditionalCssClasses,
			callback: function() {
				// Not the best place here - this is only relevant when this flow is started from the shopping list item form
				// (to select the correct shopping list on return)
				if (GetUriParam("list") !== undefined) {
					embedded += "&list=" + GetUriParam("list");
				}

				Grocy.Components.ProductPicker.PopupOpen = false;
				window.location.href = U('/product/new?flow=InplaceNewProductWithName&name=' + encodeURIComponent(lastProductSearchTerm) + '&returnto=' + encodeURIComponent(Grocy.CurrentUrlRelative + "?flow=InplaceNewProductWithName&" + embedded) + "&" + embedded);
			}
		},
		addbarcode: {
			label: '<strong>B</strong> ' + __t('Add as barcode to existing product'),
			className: 'btn-info add-new-barcode-dialog-button responsive-button',
			callback: function() {
				Grocy.Components.ProductPicker.PopupOpen = false;
				window.location.href = U(Grocy.CurrentUrlRelative + '?flow=InplaceAddBarcodeToExistingProduct&barcode=' + encodeURIComponent(lastProductSearchTerm) + "&" + embedded);
			}
		},
		addnewproductwithbarcode: {
			label: '<strong>A</strong> ' + __t('Add as new product and prefill barcode'),
			className: 'btn-warning add-new-product-with-barcode-dialog-button responsive-button ' + addProductWorkflowsAdditionalCssClasses,
			callback: function() {
				Grocy.Components.ProductPicker.PopupOpen = false;
				window.location.href = U('/product/new?flow=InplaceNewProductWithBarcode&barcode=' + encodeURIComponent(lastProductSearchTerm) + '&returnto=' + encodeURIComponent(Grocy.CurrentUrlRelative + "?flow=InplaceAddBarcodeToExistingProduct&barcode=" + lastProductSearchTerm + "&" + embedded) + "&" + embedded);
			}
		}
	};

	if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_DISABLE_BROWSER_BARCODE_CAMERA_SCANNING) {
		buttons.retrycamerascanning = {
			label: '<strong>C</strong> <i class="fas fa-camera"></i>',
			className: 'btn-primary responsive-button retry-camera-scanning-button',
			callback: function() {
				Grocy.Components.ProductPicker.PopupOpen = false;
				Grocy.Components.ProductPicker.Clear();
				$("#barcodescanner-start-button").trigger('click');
			}
		};
	}

	// The product picker contains only in-stock products on some pages,
	// so only show the workflow dialog when the entered input
	// does not match in existing product (name) or barcode,
	// otherwise an error validation message that the product is not in stock
	var existsAsProduct = false;
	var existsAsBarcode = false;
	Grocy.Api.Get('objects/product_barcodes?query[]=barcode=' + lastProductSearchTerm,
		function(barcodeResult) {
			if (barcodeResult.length > 0) {
				existsAsProduct = true;
			}

			Grocy.Api.Get('objects/products?query[]=name=' + lastProductSearchTerm,
				function(productResult) {
					if (productResult.length > 0) {
						existsAsProduct = true;
					}

					if (!existsAsBarcode && !existsAsProduct) {
						Grocy.Components.ProductPicker.PopupOpen = true;
						bootbox.dialog({
							message: __t('"%s" could not be resolved to a product, how do you want to proceed?', lastProductSearchTerm),
							title: __t('Create or assign product'),
							onEscape: function() {
								Grocy.Components.ProductPicker.PopupOpen = false;
								Grocy.Components.ProductPicker.Clear();
							},
							size: 'large',
							backdrop: true,
							closeButton: false,
							buttons: buttons
						}).on('keypress', function(e) {
							if (e.key === 'B' || e.key === 'b') {
								$('.add-new-barcode-dialog-button').not(".d-none").trigger('click');
							}
							if (e.key === 'p' || e.key === 'P') {
								$('.add-new-product-dialog-button').not(".d-none").trigger('click');
							}
							if (e.key === 'a' || e.key === 'A') {
								$('.add-new-product-with-barcode-dialog-button').not(".d-none").trigger('click');
							}
							if (e.key === 'c' || e.key === 'C') {
								$('.retry-camera-scanning-button').not(".d-none").trigger('click');
							}
						});
					} else {
						Grocy.Components.ProductAmountPicker.Reset();
						Grocy.Components.ProductPicker.Clear();
						Grocy.FrontendHelpers.ValidateForm('consume-form');
						Grocy.Components.ProductPicker.ShowCustomError(__t('This product is not in stock'));
						Grocy.Components.ProductPicker.Focus();
					}
				},
				function(xhr) {
					console.error(xhr);
				}
			);
		},
		function(xhr) {
			console.error(xhr);
		}
	);
});
