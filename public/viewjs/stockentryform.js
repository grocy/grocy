$('#save-stockentry-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#stockentry-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("stockentry-form");

	if (!jsonForm.price.toString().isEmpty())
	{
		price = parseFloat(jsonForm.price).toFixed(2);
	}

	var jsonData = { };
	jsonData.amount = jsonForm.amount;
	jsonData.best_before_date = Grocy.Components.DateTimePicker.GetValue();
	jsonData.purchased_date = Grocy.Components.DateTimePicker2.GetValue();
	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) {
		jsonData.shopping_location_id = Grocy.Components.ShoppingLocationPicker.GetValue();
	}
	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
	{
		jsonData.location_id = Grocy.Components.LocationPicker.GetValue();
	}
	else
	{
		jsonData.location_id = 1;
	}
	jsonData.price = price;

	jsonData.open = $("#open").is(":checked");

	Grocy.Api.Put("stock/entry/" + Grocy.EditObjectId, jsonData,
		function(result)
		{
			var successMessage = __t('Stock entry successfully updated') + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBookingEntry(\'' + result.id + '\',\'' + Grocy.EditObjectId + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';

			window.parent.postMessage(WindowMessageBag("StockEntryChanged", Grocy.EditObjectId), Grocy.BaseUrl);
			window.parent.postMessage(WindowMessageBag("ShowSuccessMessage", successMessage), Grocy.BaseUrl);
			window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
			window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("stockentry-form");
			console.error(xhr);
		}
	);
});

Grocy.FrontendHelpers.ValidateForm('stockentry-form');

$('#stockentry-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('stockentry-form');
});

$('#stockentry-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('stockentry-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-stockentry-button').click();
		}
	}
});

Grocy.Components.DateTimePicker.GetInputElement().on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('stockentry-form');
});

Grocy.Components.DateTimePicker.GetInputElement().on('keypress', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('stockentry-form');
});

Grocy.Components.DateTimePicker2.GetInputElement().on('change', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('stockentry-form');
});

Grocy.Components.DateTimePicker2.GetInputElement().on('keypress', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('stockentry-form');
});

Grocy.Api.Get('stock/products/' + Grocy.EditObjectProductId,
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
	function (xhr)
	{
		console.error(xhr);
	}
);

$("#amount").on("focus", function(e)
{
	$(this).select();
});
$("#amount").focus();
Grocy.FrontendHelpers.ValidateForm("stockentry-form");
