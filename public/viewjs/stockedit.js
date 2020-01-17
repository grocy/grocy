$(document).ready(function() {
	var stockRowId = GetUriParam('stockRowId');
	Grocy.Api.Get("stock/" + stockRowId + "/entry",
		function(stockEntry)
		{
			Grocy.Components.LocationPicker.SetId(stockEntry.location_id);
			$('#amount').val(stockEntry.amount);
			$('#price').val(stockEntry.price);
			Grocy.Components.DateTimePicker.SetValue(stockEntry.best_before_date);

			Grocy.Api.Get('stock/products/' + stockEntry.product_id,
				function(productDetails)
				{
					$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);

					if (productDetails.product.allow_partial_units_in_stock == 1)
					{
						$("#amount").attr("min", "0.01");
						$("#amount").attr("step", "0.01");
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %1$s', 0.01.toLocaleString()));
					}
					else
					{
						$("#amount").attr("min", "1");
						$("#amount").attr("step", "1");
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %1$s', '1'));
					}

					if (productDetails.product.enable_tare_weight_handling == 1)
					{
						$("#amount").attr("min", productDetails.product.tare_weight);
						$("#amount").parent().find(".invalid-feedback").text(__t('The amount cannot be lower than %1$s', parseFloat(productDetails.product.tare_weight).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: 2 })));
						$("#tare-weight-handling-info").removeClass("d-none");
					}
					else
					{
						$("#tare-weight-handling-info").addClass("d-none");
					}

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
} );

$('#save-stockedit-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#stockedit-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("stockedit-form");

	if (!jsonForm.price.toString().isEmpty())
	{
		price = parseFloat(jsonForm.price).toFixed(2);
	}

	var jsonData = { };
	jsonData.amount = jsonForm.amount;
	jsonData.best_before_date = Grocy.Components.DateTimePicker.GetValue();
	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	{
		jsonData.location_id = Grocy.Components.LocationPicker.GetValue();
	}
	else
	{
		jsonData.location_id = 1;
	}
	jsonData.price = price;

	var bookingResponse = null;

	var stockRowId = GetUriParam('stockRowId');
	jsonData.id = stockRowId;

	Grocy.Api.Put("stock", jsonData,
		function(result)
		{
			var successMessage = __t('Stock entry successfully updated') + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBookingEntry(\'' + result.id + '\',\'' + stockRowId + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';

			window.parent.postMessage(WindowMessageBag("StockDetailChanged", stockRowId), Grocy.BaseUrl);
			window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
			window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
			window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("stockedit-form");
			console.error(xhr);
		}
	);
});

Grocy.FrontendHelpers.ValidateForm('stockedit-form');

$('#stockedit-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('stockedit-form');
});

$('#stockedit-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('stockedit-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-stockedit-button').click();
		}
	}
});

if (Grocy.Components.DateTimePicker)
{
	Grocy.Components.DateTimePicker.GetInputElement().on('change', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('stockedit-form');
	});

	Grocy.Components.DateTimePicker.GetInputElement().on('keypress', function(e)
	{
		Grocy.FrontendHelpers.ValidateForm('stockedit-form');
	});
}
