Grocy.Components.ProductCard = {};

Grocy.Components.ProductCard.Refresh = function(productId)
{
	Grocy.Api.Get('stock/products/' + productId,
		function(productDetails)
		{
			var stockAmount = productDetails.stock_amount || '0';
			var stockValue = productDetails.stock_value || '0';
			var stockAmountOpened = productDetails.stock_amount_opened || '0';
			$('#productcard-product-name').text(productDetails.product.name);
			$('#productcard-product-description').html(productDetails.product.description);
			$('#productcard-product-stock-amount').text(stockAmount);
			$('#productcard-product-stock-qu-name').text(__n(stockAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural, true));
			$('#productcard-product-stock-value').text(stockValue);
			$('#productcard-product-last-purchased').text((productDetails.last_purchased || '2999-12-31').substring(0, 10));
			$('#productcard-product-last-purchased-timeago').attr("datetime", productDetails.last_purchased || '2999-12-31');
			$('#productcard-product-last-used').text((productDetails.last_used || '2999-12-31').substring(0, 10));
			$('#productcard-product-last-used-timeago').attr("datetime", productDetails.last_used || '2999-12-31');
			if (productDetails.location != null)
			{
				$('#productcard-product-location').text(productDetails.location.name);
			}
			$('#productcard-product-spoil-rate').text((parseFloat(productDetails.spoil_rate_percent) / 100).toLocaleString(undefined, { style: "percent" }));

			if (productDetails.is_aggregated_amount == 1)
			{
				$('#productcard-product-stock-amount-aggregated').text(productDetails.stock_amount_aggregated);
				$('#productcard-product-stock-qu-name-aggregated').text(__n(productDetails.stock_amount_aggregated, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural, true));

				if (productDetails.stock_amount_opened_aggregated > 0)
				{
					$('#productcard-product-stock-opened-amount-aggregated').text(__t('%s opened', productDetails.stock_amount_opened_aggregated));
				}
				else
				{
					$('#productcard-product-stock-opened-amount-aggregated').text("");
				}

				$("#productcard-aggregated-amounts").removeClass("d-none");
			}
			else
			{
				$("#productcard-aggregated-amounts").addClass("d-none");
			}

			if (productDetails.product.description != null && !productDetails.product.description.isEmpty())
			{
				$("#productcard-product-description-wrapper").removeClass("d-none");
			}
			else
			{
				$("#productcard-product-description-wrapper").addClass("d-none");
			}

			if (productDetails.average_shelf_life_days == -1)
			{
				$('#productcard-product-average-shelf-life').text(__t("Unknown"));
			}
			else if (parseInt(productDetails.average_shelf_life_days) > 73000) // > 200 years aka forever
			{
				$('#productcard-product-average-shelf-life').text(__t("Unlimited"));
			}
			else
			{
				$('#productcard-product-average-shelf-life').text(moment.duration(productDetails.average_shelf_life_days, "days").humanize());
			}

			if (stockAmountOpened > 0)
			{
				$('#productcard-product-stock-opened-amount').text(__t('%s opened', stockAmountOpened));
			}
			else
			{
				$('#productcard-product-stock-opened-amount').text("");
			}

			$('#productcard-product-edit-button').attr("href", U("/product/" + productDetails.product.id.toString() + '?' + 'returnto=' + encodeURIComponent(Grocy.CurrentUrlRelative)));
			$('#productcard-product-journal-button').attr("href", U("/stockjournal?embedded&product=" + productDetails.product.id.toString()));
			$('#productcard-product-stock-button').attr("href", U("/stockentries?embedded&product=" + productDetails.product.id.toString()));
			$('#productcard-product-stock-button').removeClass("disabled");
			$('#productcard-product-edit-button').removeClass("disabled");
			$('#productcard-product-journal-button').removeClass("disabled");

			if (productDetails.last_price !== null)
			{
				$('#productcard-product-last-price').text(__t("%1$s per %2$s", (Number.parseFloat(productDetails.last_price) * Number.parseFloat(productDetails.product.qu_factor_purchase_to_stock)).toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }), productDetails.default_quantity_unit_purchase.name));
				$('#productcard-product-last-price').attr("data-original-title", __t("%1$s per %2$s", Number.parseFloat(productDetails.last_price).toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }), productDetails.quantity_unit_stock.name));
			}
			else
			{
				$('#productcard-product-last-price').text(__t('Unknown'));
				$('#productcard-product-last-price').removeAttr("data-original-title");
			}

			if (productDetails.avg_price !== null)
			{
				$('#productcard-product-average-price').text(__t("%1$s per %2$s", (Number.parseFloat(productDetails.avg_price) * Number.parseFloat(productDetails.product.qu_factor_purchase_to_stock)).toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }), productDetails.default_quantity_unit_purchase.name));
				$('#productcard-product-average-price').attr("data-original-title", __t("%1$s per %2$s", Number.parseFloat(productDetails.avg_price).toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }), productDetails.quantity_unit_stock.name));
			}
			else
			{
				$('#productcard-product-average-price').text(__t('Unknown'));
				$().removeAttr("data-original-title");
			}

			if (productDetails.product.picture_file_name !== null && !productDetails.product.picture_file_name.isEmpty())
			{
				$("#productcard-product-picture").removeClass("d-none");
				$("#productcard-product-picture").attr("src", U('/api/files/productpictures/' + btoa(productDetails.product.picture_file_name) + '?force_serve_as=picture&best_fit_width=400'));
			}
			else
			{
				$("#productcard-product-picture").addClass("d-none");
			}

			$("#productcard-product-stock-amount-wrapper").removeClass("d-none");
			$("#productcard-aggregated-amounts").addClass("pl-2");
			if (productDetails.product.no_own_stock == 1)
			{
				$("#productcard-product-stock-amount-wrapper").addClass("d-none");
				$("#productcard-aggregated-amounts").removeClass("pl-2");
			}

			RefreshContextualTimeago(".productcard");
			RefreshLocaleNumberDisplay(".productcard");

			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				Grocy.Api.Get('stock/products/' + productId + '/price-history',
					function(priceHistoryDataPoints)
					{
						if (priceHistoryDataPoints.length > 0)
						{
							$("#productcard-product-price-history-chart").removeClass("d-none");
							$("#productcard-no-price-data-hint").addClass("d-none");

							Grocy.Components.ProductCard.ReInitPriceHistoryChart();
							var datasets = {};
							var chart = Grocy.Components.ProductCard.PriceHistoryChart.data;
							priceHistoryDataPoints.forEach((dataPoint) =>
							{
								var key = __t("Unknown store");
								if (dataPoint.shopping_location)
								{
									key = dataPoint.shopping_location.name
								}

								if (!datasets[key])
								{
									datasets[key] = []
								}
								chart.labels.push(moment(dataPoint.date).toDate());
								datasets[key].push({ x: moment(dataPoint.date).toDate(), y: Number.parseFloat(dataPoint.price) * Number.parseFloat(productDetails.product.qu_factor_purchase_to_stock) });

							});
							Object.keys(datasets).forEach((key) =>
							{
								chart.datasets.push({
									data: datasets[key],
									fill: false,
									borderColor: "HSL(" + (129 * chart.datasets.length) + ",100%,50%)",
									label: key,
								});
							});

							Grocy.Components.ProductCard.PriceHistoryChart.update();
						}
						else
						{
							$("#productcard-product-price-history-chart").addClass("d-none");
							$("#productcard-no-price-data-hint").removeClass("d-none");
						}
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};

Grocy.Components.ProductCard.ReInitPriceHistoryChart = function()
{
	if (typeof Grocy.Components.ProductCard.PriceHistoryChart !== "undefined")
	{
		Grocy.Components.ProductCard.PriceHistoryChart.destroy();
	}

	var format = 'YYYY-MM-DD';
	Grocy.Components.ProductCard.PriceHistoryChart = new Chart(document.getElementById("productcard-product-price-history-chart"), {
		type: "line",
		data: {
			labels: [ //Date objects
				// Will be populated in Grocy.Components.ProductCard.Refresh
			],
			datasets: [ //Datasets
				// Will be populated in Grocy.Components.ProductCard.Refresh
			]
		},
		options: {
			scales: {
				xAxes: [{
					type: 'time',
					time: {
						parser: format,
						round: 'day',
						tooltipFormat: format,
						unit: 'day',
						unitStepSize: 10,
						displayFormats: {
							'day': format
						}
					},
					ticks: {
						autoSkip: true,
						maxRotation: 0
					}
				}],
				yAxes: [{
					ticks: {
						beginAtZero: true,
						callback: function(value, index, ticks)
						{
							return Number.parseFloat(value).toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display });
						}
					}
				}]
			},
			legend: {
				display: true
			},
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data)
					{
						var label = data.datasets[tooltipItem.datasetIndex].label || '';

						if (label)
						{
							label += ': ';
						}

						label += tooltipItem.yLabel.toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display })
						return label;
					}
				}
			}
		}
	});
}

$("#productcard-product-description").on("shown.bs.collapse", function()
{
	$(".expandable-text").find("a[data-toggle='collapse']").text(__t("Show less"));
})

$("#productcard-product-description").on("hidden.bs.collapse", function()
{
	$(".expandable-text").find("a[data-toggle='collapse']").text(__t("Show more"));
})
