Grocy.Components.ProductAmountPicker = {};
Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled = false;

Grocy.Components.ProductAmountPicker.Reload = function(productId, destinationQuId, forceInitialDisplayQu = false)
{
	var conversionsForProduct = FindAllObjectsInArrayByPropertyValue(Grocy.QuantityUnitConversionsResolved, 'product_id', productId);

	if (!Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled)
	{
		$("#qu_id").find("option").remove().end();
		if (!$("#qu_id").hasAttr("required"))
		{
			$("#qu_id").append('<option></option>');
		}

		$("#qu_id").attr("data-destination-qu-name", FindObjectInArrayByPropertyValue(Grocy.QuantityUnits, 'id', destinationQuId).name);
		$("#qu_id").attr("data-destination-qu-name-plural", FindObjectInArrayByPropertyValue(Grocy.QuantityUnits, 'id', destinationQuId).name_plural);

		conversionsForProduct.forEach(conversion =>
		{
			if (conversion.to_qu_id == destinationQuId)
			{
				conversion.factor = 1;
			}

			// Only conversions related to the destination QU are needed
			// + only add one conversion per to_qu_id (multiple ones can be a result of contradictory definitions = user input bullshit)
			if ((conversion.from_qu_id == destinationQuId || conversion.to_qu_id == destinationQuId) && !$('#qu_id option[value="' + conversion.to_qu_id + '"]').length)
			{
				$("#qu_id").append('<option value="' + conversion.to_qu_id + '" data-qu-factor="' + conversion.factor + '" data-qu-name-plural="' + conversion.to_qu_name_plural + '">' + conversion.to_qu_name + '</option>');
			}
		});
	}

	if (!Grocy.Components.ProductAmountPicker.InitialValueSet || forceInitialDisplayQu)
	{
		$("#qu_id").val($("#qu_id").attr("data-initial-qu-id"));
	}

	if (!Grocy.Components.ProductAmountPicker.InitialValueSet)
	{
		var convertedAmount = ($("#display_amount").val() * $("#qu_id option:selected").attr("data-qu-factor")).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts });
		$("#display_amount").val(convertedAmount);

		Grocy.Components.ProductAmountPicker.InitialValueSet = true;
	}

	if (conversionsForProduct.length === 1 && !forceInitialDisplayQu)
	{
		$("#qu_id").val($("#qu_id option:first").val());
	}

	if ($('#qu_id option').length == 1)
	{
		$("#qu_id").attr("disabled", "");
	}
	else
	{
		$("#qu_id").removeAttr("disabled");
	}

	$(".input-group-productamountpicker").trigger("change");
}

Grocy.Components.ProductAmountPicker.SetQuantityUnit = function(quId)
{
	$("#qu_id").val(quId);
}

Grocy.Components.ProductAmountPicker.AllowAnyQu = function(keepInitialQu = false)
{
	Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled = true;

	$("#qu_id").find("option").remove().end();
	if (!$("#qu_id").hasAttr("required"))
	{
		$("#qu_id").append('<option></option>');
	}

	Grocy.QuantityUnits.forEach(qu =>
	{
		$("#qu_id").append('<option value="' + qu.id + '" data-qu-factor="1" data-qu-name-plural="' + qu.name_plural + '">' + qu.name + '</option>');
	});

	if (keepInitialQu)
	{
		Grocy.Components.ProductAmountPicker.SetQuantityUnit($("#qu_id").attr("data-initial-qu-id"));
	}

	$("#qu_id").removeAttr("disabled");

	$(".input-group-productamountpicker").trigger("change");
}

Grocy.Components.ProductAmountPicker.Reset = function()
{
	$("#qu_id").find("option").remove();
	$("#qu-conversion-info").addClass("d-none");
	$("#qu-display_amount-info").val("");
}

$(".input-group-productamountpicker").on("change", function()
{
	var selectedQuName = $("#qu_id option:selected").text();
	var quFactor = $("#qu_id option:selected").attr("data-qu-factor");
	var amount = $("#display_amount").val();
	var destinationAmount = amount / quFactor;
	var destinationQuName = __n(destinationAmount, $("#qu_id").attr("data-destination-qu-name"), $("#qu_id").attr("data-destination-qu-name-plural"), true);

	if ($("#qu_id").attr("data-destination-qu-name") == selectedQuName || Grocy.Components.ProductAmountPicker.AllowAnyQuEnabled || !amount || !selectedQuName)
	{
		$("#qu-conversion-info").addClass("d-none");
	}
	else
	{
		$("#qu-conversion-info").removeClass("d-none");
		$("#qu-conversion-info").text(__t("This equals %1$s %2$s", destinationAmount.toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }), destinationQuName));
	}

	var n = Grocy.UserSettings.stock_decimal_places_amounts;
	if (n <= 0)
	{
		n = 1;
	}

	$("#amount").val(destinationAmount.toFixed(n).replace(/0*$/g, '')).trigger("change");
});

$("#display_amount").on("keyup", function()
{
	$(".input-group-productamountpicker").trigger("change");
});
