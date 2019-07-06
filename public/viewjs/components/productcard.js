Grocy.Components.ProductCard = { };

Grocy.Components.ProductCard.Refresh = function(productId)
{
	Grocy.Api.Get('stock/products/' + productId,
		function(productDetails)
		{
			var stockAmount = productDetails.stock_amount || '0';
			var stockAmountOpened = productDetails.stock_amount_opened || '0';
			$('#productcard-product-name').text(productDetails.product.name);
			$('#productcard-product-description').text(productDetails.product.description);
			$('#productcard-product-stock-amount').text(stockAmount);
			$('#productcard-product-stock-qu-name').text(__n(stockAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural));
			$('#productcard-product-last-purchased').text((productDetails.last_purchased || __t('never')).substring(0, 10));
			$('#productcard-product-last-purchased-timeago').attr("datetime", productDetails.last_purchased || '');
			$('#productcard-product-last-used').text((productDetails.last_used || __t('never')).substring(0, 10));
			$('#productcard-product-last-used-timeago').attr("datetime", productDetails.last_used || '');
			$('#productcard-product-location').text(productDetails.location.name);
			$('#productcard-product-spoil-rate').text(parseFloat(productDetails.spoil_rate_percent).toLocaleString(undefined, { style: "percent" }));

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

			$('#productcard-product-edit-button').attr("href", U("/product/" + productDetails.product.id.toString()));
			$('#productcard-product-edit-button').removeClass("disabled");

			if (productDetails.last_price !== null)
			{
				$('#productcard-product-last-price').text(Number.parseFloat(productDetails.last_price).toLocaleString() + ' ' + Grocy.Currency);
			}
			else
			{
				$('#productcard-product-last-price').text(__t('Unknown'));
			}

			if (productDetails.product.picture_file_name !== null && !productDetails.product.picture_file_name.isEmpty())
			{
				$("#productcard-no-product-picture").addClass("d-none");
				$("#productcard-product-picture").removeClass("d-none");
				$("#productcard-product-picture").attr("src", U('/api/files/productpictures/' + btoa(productDetails.product.picture_file_name)));
			}
			else
			{
				$("#productcard-no-product-picture").removeClass("d-none");
				$("#productcard-product-picture").addClass("d-none");
			}

			EmptyElementWhenMatches('#productcard-product-last-purchased-timeago', __t('timeago_nan'));
			EmptyElementWhenMatches('#productcard-product-last-used-timeago', __t('timeago_nan'));
			RefreshContextualTimeago();
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);

	Grocy.Api.Get('stock/products/' + productId + '/price-history',
		function(priceHistoryDataPoints)
		{
			if (priceHistoryDataPoints.length > 0)
			{
				$("#productcard-product-price-history-chart").removeClass("d-none");
				$("#productcard-no-price-data-hint").addClass("d-none");

				Grocy.Components.ProductCard.ReInitPriceHistoryChart();
				priceHistoryDataPoints.forEach((dataPoint) =>
				{
					Grocy.Components.ProductCard.PriceHistoryChart.data.labels.push(moment(dataPoint.date).toDate());

					var dataset = Grocy.Components.ProductCard.PriceHistoryChart.data.datasets[0];
					dataset.data.push(dataPoint.price);
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
			datasets: [{
				data: [
					// Will be populated in Grocy.Components.ProductCard.Refresh
				],
				fill: false,
				borderColor: '%s7a2b8'
			}]
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
						beginAtZero: true
					}
				}]
			},
			legend: {
				display: false
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
