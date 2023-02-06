$(".numberpicker-down-button").unbind('click').on("click", function()
{
	var inputElement = $(this).parent().parent().find('input[type="number"]');
	inputElement.val(Number.parseFloat(inputElement.val() || 1) - 1);
	inputElement.trigger('keyup');
	inputElement.trigger('change');
});

$(".numberpicker-up-button").unbind('click').on("click", function()
{
	var inputElement = $(this).parent().parent().find('input[type="number"]');
	inputElement.val(Number.parseFloat(inputElement.val() || 0) + 1);
	inputElement.trigger('keyup');
	inputElement.trigger('change');
});

$(".numberpicker").on("keyup", function()
{
	if ($(this).attr("data-not-equal") && $(this).attr("data-not-equal") == $(this).val())
	{
		$(this)[0].setCustomValidity("error");
	}
	else
	{
		$(this)[0].setCustomValidity("");
	}
});

$(".numberpicker").each(function()
{
	new MutationObserver(function(mutations)
	{
		mutations.forEach(function(mutation)
		{
			if (mutation.type == "attributes" && (mutation.attributeName == "min" || mutation.attributeName == "max" || mutation.attributeName == "data-not-equal" || mutation.attributeName == "data-initialised"))
			{
				var element = $(mutation.target);
				var min = element.attr("min");
				var decimals = element.attr("data-decimals");

				var max = "";
				if (element.hasAttr("max"))
				{
					max = element.attr("max");
				}

				if (element.hasAttr("data-not-equal"))
				{
					var notEqual = element.attr("data-not-equal");

					if (notEqual != "NaN")
					{
						if (!max)
						{
							element.parent().find(".invalid-feedback").text(__t("This cannot be lower than %1$s or equal %2$s and needs to be a valid number with max. %3$s decimal places", Number.parseFloat(min).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: decimals }), Number.parseFloat(notEqual).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: decimals }), decimals));
						}
						else
						{
							element.parent().find(".invalid-feedback").text(__t("This must be between %1$s and %2$s, cannot equal %3$s and needs to be a valid number with max. %4$s decimal places", Number.parseFloat(min).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: decimals }), Number.parseFloat(max).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: decimals }), Number.parseFloat(notEqual).toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals }), decimals));
						}

						return;
					}
				}

				if (!max)
				{
					element.parent().find(".invalid-feedback").text(__t("This cannot be lower than %1$s and needs to be a valid number with max. %2$s decimal places", Number.parseFloat(min).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: decimals }), decimals));
				}
				else
				{
					element.parent().find(".invalid-feedback").text(__t("This must between %1$s and %2$s and needs to be a valid number with max. %3$s decimal places", Number.parseFloat(min).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: decimals }), Number.parseFloat(max).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: decimals }), decimals));
				}
			}
		});
	}).observe(this, {
		attributes: true
	});
});
$(".numberpicker").attr("data-initialised", "true"); // Dummy change to trigger MutationObserver above once

$(".numberpicker").on("keydown", function(e)
{
	if (e.key == "ArrowUp")
	{
		e.preventDefault();
		$(this).parent().find(".numberpicker-up-button").click();
	}
	else if (e.key == "ArrowDown")
	{
		e.preventDefault();
		$(this).parent().find(".numberpicker-down-button").click();
	}
});

$(".numberpicker.locale-number-input.locale-number-currency").on("blur", function()
{
	if (BoolVal(Grocy.UserSettings.stock_auto_decimal_separator_prices))
	{
		var value = this.value.toString();
		if (!value || value.includes(".") || value.includes(","))
		{
			return;
		}

		var decimalPlaces = Grocy.UserSettings.stock_decimal_places_prices_input;
		if (value.length <= decimalPlaces)
		{
			value = value.padStart(decimalPlaces, "0");
		}

		var valueNew = Number.parseFloat(value.substring(0, value.length - decimalPlaces) + '.' + value.slice(decimalPlaces * -1));
		$(this).val(valueNew);
	}
});
