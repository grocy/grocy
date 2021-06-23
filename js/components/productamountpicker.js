class productamountpicker
{
	constructor(Grocy, scopeSelector = null)
	{
		this.Grocy = Grocy;

		this.scopeSelector = scopeSelector;
		this.scope = scopeSelector != null ? $(scopeSelector) : $(document);
		this.$ = scopeSelector != null ? $(scopeSelector).find : $;

		Grocy.Use("numberpicker");
		this.AllowAnyQuEnabled = false;

		this.qu_id = this.$("#qu_id");
		this.display_amount = this.$("#display_amount");

		var self = this;

		this.$(".input-group-productamountpicker").on("change", () => self.onChangeHandler(this));
		this.$("#display_amount").on("keyup", () => self.$(".input-group-productamountpicker").trigger("change"));
	}

	Reload(productId, destinationQuId, forceInitialDisplayQu = false)
	{
		var conversionsForProduct = this.Grocy.QuantityUnitConversionsResolved.filter(elem => elem.product_id == productId);

		if (!this.AllowAnyQuEnabled)
		{
			var qu = this.Grocy.QuantityUnits.find(elem => elem.id == destinationQuId);
			this.qu_id.find("option").remove().end();
			this.qu_id.attr("data-destination-qu-name", qu.name);
			this.qu_id.attr("data-destination-qu-name-plural", qu.name_plural);

			for (let conversion of conversionsForProduct)
			{
				var factor = parseFloat(conversion.factor);
				if (conversion.to_qu_id == destinationQuId)
				{
					factor = 1;
				}

				if (!this.$('#qu_id option[value="' + conversion.to_qu_id + '"]').length) // Don't add the destination QU multiple times
				{
					this.qu_id.append('<option value="' + conversion.to_qu_id + '" data-qu-factor="' + factor + '">' + conversion.to_qu_name + '</option>');
				}
			}
		}

		if (!this.InitialValueSet || forceInitialDisplayQu)
		{
			this.qu_id.val(this.qu_id.attr("data-initial-qu-id"));
		}

		if (!this.InitialValueSet)
		{
			var convertedAmount = this.display_amount.val() * this.$("#qu_id option:selected").attr("data-qu-factor");
			this.display_amount.val(convertedAmount);

			this.InitialValueSet = true;
		}

		if (conversionsForProduct.length === 1 && !forceInitialDisplayQu)
		{
			this.qu_id.val(this.$("#qu_id option:first").val());
		}

		if (this.$('#qu_id option').length == 1)
		{
			this.qu_id.attr("disabled", "");
		}
		else
		{
			this.qu_id.removeAttr("disabled");
		}

		this.$(".input-group-productamountpicker").trigger("change");


	}

	SetQuantityUnit(quId)
	{
		this.qu_id.val(quId);
	}

	AllowAnyQu(keepInitialQu = false)
	{
		this.AllowAnyQuEnabled = true;

		this.qu_id.find("option").remove().end();
		for (let qu of this.Grocy.QuantityUnits)
		{
			this.qu_id.append('<option value="' + qu.id + '" data-qu-factor="1">' + qu.name + '</option>');
		}

		if (keepInitialQu)
		{
			this.SetQuantityUnit(this.qu_id.attr("data-initial-qu-id"));
		}

		this.qu_id.removeAttr("disabled");

		this.$(".input-group-productamountpicker").trigger("change");
	}

	Reset()
	{
		this.qu_id.find("option").remove();
		this.$("#qu-conversion-info").addClass("d-none");
		this.$("#qu-display_amount-info").val("");
	}

	onChangeHandler(_this)
	{
		var selectedQuName = this.$("#qu_id option:selected").text();
		var quFactor = this.$("#qu_id option:selected").attr("data-qu-factor");
		var amount = this.display_amount.val();
		var destinationAmount = amount / quFactor;
		var destinationQuName = this.Grocy.translaten(destinationAmount,
			this.qu_id.attr("data-destination-qu-name"),
			this.qu_id.attr("data-destination-qu-name-plural")
		);

		var conversionInfo = this.$("#qu-conversion-info");

		if (this.qu_id.attr("data-destination-qu-name") == selectedQuName ||
			this.AllowAnyQuEnabled ||
			amount.toString().isEmpty() ||
			selectedQuName.toString().isEmpty())
		{
			conversionInfo.addClass("d-none");
		}
		else
		{
			conversionInfo.removeClass("d-none");
			conversionInfo.text(this.Grocy.translate("This equals %1$s %2$s",
				destinationAmount.toLocaleString({
					minimumFractionDigits: 0,
					maximumFractionDigits: this.Grocy.UserSettings.stock_decimal_places_amounts
				}),
				destinationQuName)
			);
		}

		$("#amount").val(
			destinationAmount
				.toFixed(this.Grocy.UserSettings.stock_decimal_places_amounts)
				.replace(/0*$/g, '')
		);
	}
}

export { productamountpicker }