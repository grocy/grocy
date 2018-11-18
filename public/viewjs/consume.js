$('#save-consume-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consume-form').serializeJSON();

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonForm.amount = 1;
	}

	var spoiled = 0;
	if ($('#spoiled').is(':checked'))
	{
		spoiled = 1;
	}

	var apiUrl = 'stock/consume-product/' + jsonForm.product_id + '/' + jsonForm.amount + '?spoiled=' + spoiled;

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		apiUrl += "&stock_entry_id=" + jsonForm.specific_stock_entry;
	}

	Grocy.Api.Get('stock/get-product-details/' + jsonForm.product_id,
		function(productDetails)
		{
			Grocy.Api.Get(apiUrl,
				function(result)
				{
					$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
					if ($("#use_specific_stock_entry").is(":checked"))
					{
						$("#use_specific_stock_entry").click();
					}

					toastr.success(L('Removed #1 #2 of #3 from stock', jsonForm.amount, Pluralize(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBooking(' + result.booking_id + ')"><i class="fas fa-undo"></i> ' + L("Undo") + '</a>');

					$('#amount').val(1);
					Grocy.Components.ProductPicker.SetValue('');
					Grocy.Components.ProductPicker.GetInputElement().focus();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$('#save-mark-as-open-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consume-form').serializeJSON();

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		jsonForm.amount = 1;
	}

	var apiUrl = 'stock/open-product/' + jsonForm.product_id + '/' + jsonForm.amount;

	if ($("#use_specific_stock_entry").is(":checked"))
	{
		apiUrl += "&stock_entry_id=" + jsonForm.specific_stock_entry;
	}

	Grocy.Api.Get('stock/get-product-details/' + jsonForm.product_id,
		function(productDetails)
		{
			Grocy.Api.Get(apiUrl,
				function(result)
				{
					$("#specific_stock_entry").find("option").remove().end().append("<option></option>");
					if ($("#use_specific_stock_entry").is(":checked"))
					{
						$("#use_specific_stock_entry").click();
					}

					toastr.success(L('Marked #1 #2 of #3 as opened', jsonForm.amount, Pluralize(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBooking(' + result.booking_id + ')"><i class="fas fa-undo"></i> ' + L("Undo") + '</a>');

					$('#amount').val(1);
					Grocy.Components.ProductPicker.SetValue('');
					Grocy.Components.ProductPicker.GetInputElement().focus();
					Grocy.FrontendHelpers.ValidateForm('consume-form');
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
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

		Grocy.Api.Get('stock/get-product-details/' + productId,
			function(productDetails)
			{
				$('#amount').attr('max', productDetails.stock_amount);
				$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);

				if ((productDetails.stock_amount || 0) === 0)
				{
					Grocy.Components.ProductPicker.SetValue('');
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

		Grocy.Api.Get("stock/get-product-stock-entries/" + productId,
			function (stockEntries)
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
	Grocy.Api.Get('stock/undo-booking/' + bookingId.toString(),
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
