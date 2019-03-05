$('#save-consume-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consume-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("consume-form");

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonForm.amount = 1;
	}

	var apiUrl = 'stock/products/' + jsonForm.product_id + '/consume';

	var jsonData = {};
	jsonData.amount = jsonForm.amount;
	jsonData.spoiled = $('#spoiled').is(':checked');

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonData.stock_entry_id = jsonForm.specific_stock_entry;
	}

	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_RECIPES && Grocy.Components.RecipePicker.GetValue().toString().length > 0)
	{
		jsonData.recipe_id = Grocy.Components.RecipePicker.GetValue();
	}

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			Grocy.Api.Post(apiUrl, jsonData,
				function(result)
				{
					$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
					if ($("#use_specific_stock_entry").is(":checked"))
					{
						$("#use_specific_stock_entry").click();
					}

					Grocy.FrontendHelpers.EndUiBusy("consume-form");
					toastr.success(L('Removed #1 #2 of #3 from stock', Math.abs(result.amount), Pluralize(Math.abs(result.amount), productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBooking(' + result.id + ')"><i class="fas fa-undo"></i> ' + L("Undo") + '</a>');

					$("#amount").attr("min", "1");
					$("#amount").attr("max", "999999");
					$("#amount").attr("step", "1");
					$("#amount").parent().find(".invalid-feedback").text(L('The amount cannot be lower than #1', '1'));
					$('#amount').val(1);
					$("#tare-weight-handling-info").addClass("d-none");
					Grocy.Components.ProductPicker.Clear();
					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_RECIPES)
					{
						Grocy.Components.RecipePicker.Clear();
					}
					Grocy.Components.ProductPicker.GetInputElement().focus();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("consume-form");
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("consume-form");
			console.error(xhr);
		}
	);
});

$('#save-mark-as-open-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consume-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("consume-form");

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonForm.amount = 1;
	}

	var apiUrl = 'stock/products/' + jsonForm.product_id + '/open';

	jsonData = { };
	jsonData.amount = jsonForm.amount;

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonData.stock_entry_id = jsonForm.specific_stock_entry;
	}

	Grocy.Api.Get('stock/products/' + jsonForm.product_id,
		function(productDetails)
		{
			Grocy.Api.Post(apiUrl, jsonData,
				function(result)
				{
					$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
					if ($("#use_specific_stock_entry").is(":checked"))
					{
						$("#use_specific_stock_entry").click();
					}

					Grocy.FrontendHelpers.EndUiBusy("consume-form");
					toastr.success(L('Marked #1 #2 of #3 as opened', jsonForm.amount, Pluralize(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBooking(' + result.id + ')"><i class="fas fa-undo"></i> ' + L("Undo") + '</a>');

					$('#amount').val(1);
					Grocy.Components.ProductPicker.Clear();
					Grocy.Components.ProductPicker.GetInputElement().focus();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("consume-form");
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("consume-form");
			console.error(xhr);
		}
	);
});

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
	if ($("#use_specific_stock_entry").is(":checked"))
	{
		$("#use_specific_stock_entry").click();
	}

	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Components.ProductCard.Refresh(productId);

		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				$('#amount').attr('max', productDetails.stock_amount);
				$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);

				if (productDetails.product.allow_partial_units_in_stock == 1)
				{
					$("#amount").attr("min", "0.01");
					$("#amount").attr("step", "0.01");
					$("#amount").parent().find(".invalid-feedback").text(L('The amount must be between #1 and #2', 0.01.toLocaleString(), parseFloat(productDetails.stock_amount).toLocaleString()));
				}
				else
				{
					$("#amount").attr("min", "1");
					$("#amount").attr("step", "1");
					$("#amount").parent().find(".invalid-feedback").text(L('The amount must be between #1 and #2', "1", parseFloat(productDetails.stock_amount).toLocaleString()));
				}

				if (productDetails.product.enable_tare_weight_handling == 1)
				{
					$("#amount").attr("min", productDetails.product.tare_weight);
					$('#amount').attr('max', parseFloat(productDetails.stock_amount) + parseFloat(productDetails.product.tare_weight));
					$("#amount").parent().find(".invalid-feedback").text(L('The amount must be between #1 and #2', parseFloat(productDetails.product.tare_weight).toLocaleString(), (parseFloat(productDetails.stock_amount) + parseFloat(productDetails.product.tare_weight)).toLocaleString()));
					$("#tare-weight-handling-info").removeClass("d-none");
				}
				else
				{
					$("#tare-weight-handling-info").addClass("d-none");
				}

				if ((productDetails.stock_amount || 0) === 0)
				{
					Grocy.Components.ProductPicker.Clear();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
					Grocy.Components.ProductPicker.ShowCustomError(L('This product is not in stock'));
					Grocy.Components.ProductPicker.GetInputElement().focus();
				}
				else
				{
					Grocy.Components.ProductPicker.HideCustomError();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
					$('#amount').focus();
				}

				if (productDetails.stock_amount == productDetails.stock_amount_opened)
				{
					$("#save-mark-as-open-button").addClass("disabled");
				}
				else
				{
					$("#save-mark-as-open-button").removeClass("disabled");
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);

		Grocy.Api.Get("stock/products/" + productId + '/entries',
			function(stockEntries)
			{
				stockEntries.forEach(stockEntry =>
				{
					var openTxt = L("Not opened");
					if (stockEntry.open == 1)
					{
						openTxt = L("Opened");
					}

					for (i = 0; i < stockEntry.amount; i++)
					{
						$("#specific_stock_entry").append($("<option>", {
							value: stockEntry.stock_id,
							text: L("Expires on #1; Bought on #2", moment(stockEntry.best_before_date).format("YYYY-MM-DD"), moment(stockEntry.purchased_date).format("YYYY-MM-DD")) + "; " + openTxt
						}));
					}
				});
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#amount').val(1);
Grocy.Components.ProductPicker.GetInputElement().focus();
Grocy.FrontendHelpers.ValidateForm('consume-form');

$('#amount').on('focus', function(e)
{
	$(this).select();
});

$('#consume-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('consume-form');
});

$('#consume-form select').change(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('consume-form');
});

$('#consume-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('consume-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-consume-button').click();
		}
	}
});

$("#use_specific_stock_entry").on("change", function()
{
	var value = $(this).is(":checked");
	if (value)
	{
		$("#specific_stock_entry").removeAttr("disabled");
		$("#amount").attr("disabled", "");
		$("#amount").val(1);
		$("#amount").removeAttr("required");
		$("#specific_stock_entry").attr("required", "");
	}
	else
	{
		$("#specific_stock_entry").attr("disabled", "");
		$("#amount").removeAttr("disabled");
		$("#amount").attr("required", "");
		$("#specific_stock_entry").removeAttr("required");
	}

	Grocy.FrontendHelpers.ValidateForm("consume-form");
});

function UndoStockBooking(bookingId)
{
	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', { },
		function(result)
		{
			toastr.success(L("Booking successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
