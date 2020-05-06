Grocy.Components.ProductAmountPicker = {};
Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled = false;

Grocy.Components.ProductAmountPicker.Reload = function(productId, destinationQuId, forceInitialDisplayQu = false)
{
	var conversionsForProduct = FindAllObjectsInArrayByPropertyValue(Grocy.QuantityUnitConversionsResolved, 'product_id', productId);

	if (!Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled)
	{
		$("#qu-id").find("option").remove().end();
		$("#qu-id").attr("data-destination-qu-name", FindObjectInArrayByPropertyValue(Grocy.QuantityUnits, 'id', destinationQuId).name);
		conversionsForProduct.forEach(conversion =>
		{
			var factor = conversion.factor;
			if (conversion.to_qu_id == destinationQuId)
			{
				$("#qu-id").append('<option value="' + conversion.from_qu_id + '" data-qu-factor="' + factor + '">' + conversion.from_qu_name + '</option>');
				factor = 1;
			}

			$("#qu-id").append('<option value="' + conversion.to_qu_id + '" data-qu-factor="' + factor + '">' + conversion.to_qu_name + '</option>');
		});
	}

	if (!Grocy.Components.ProductAmountPicker.InitialValueSet || forceInitialDisplayQu)
	{
		$("#qu-id").val($("#qu-id").attr("data-initial-qu-id"));
	}

	if (!Grocy.Components.ProductAmountPicker.InitialValueSet)
	{
		var convertedAmount = $("#display_amount").val() * $("#qu-id option:selected").attr("data-qu-factor");
		$("#display_amount").val(convertedAmount);

		Grocy.Components.ProductAmountPicker.InitialValueSet = true;
	}

	if (conversionsForProduct.length === 1 && !forceInitialDisplayQu)
	{
		$("#qu-id").val($("#qu-id option:first").val());
	}

	$(".input-group-productamountpicker").trigger("change");
}

Grocy.Components.ProductAmountPicker.SetQuantityUnit = function(quId)
{
	$("#qu-id").val(quId);
}

Grocy.Components.ProductAmountPicker.SetQuantityUnitFactorPurchaseToStock = function(quFactorPurchaseToStock)
{
	$("#qu-factor-purchase-to-stock").val(quFactorPurchaseToStock);
}

Grocy.Components.ProductAmountPicker.AllowAnyQu = function(keepInitialQu = false)
{
	Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled = true;
	
	$("#qu-id").find("option").remove().end();
	Grocy.QuantityUnits.forEach(qu =>
	{
		$("#qu-id").append('<option value="' + qu.id + '" data-qu-factor="1">' + qu.name + '</option>');
	});

	if (keepInitialQu)
	{
		Grocy.Components.ProductAmountPicker.SetQuantityUnit($("#qu-id").attr("data-initial-qu-id"));
	}

	$(".input-group-productamountpicker").trigger("change");
}

$("#qu-id").on("change", function()
{
	var destinationQuName = $("#qu_id").attr("data-destination-qu-name");
	var selectedQuName = $("#qu_id option:selected").text();
	var quFactor = $("#qu_id option:selected").attr("data-qu-factor");
	var amount = $("#display_amount").val();
	var destinationAmount = amount / quFactor;

	if (destinationQuName == selectedQuName || Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled || amount.toString().isEmpty() || selectedQuName.toString().isEmpty())
	{
		$("#qu-conversion-info").addClass("d-none");
	}
	else
	{
		$("#qu-conversion-info").removeClass("d-none");
		$("#qu-conversion-info").text(__t("This equals %1$s %2$s in stock", destinationAmount.toLocaleString(), destinationQuName));
	}

	$("#amount").val(destinationAmount.toFixed(4).replace(/0*$/g,''));
});

$("#display_amount").on("keyup", function()
{
	$(".input-group-productamountpicker").trigger("change");
});
