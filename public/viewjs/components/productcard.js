Grocy.Components.ProductCard = { };

Grocy.Components.ProductCard.Refresh = function(productId)
{
	Grocy.Api.Get('stock/get-product-details/' + productId,
		function(productDetails)
		{
			var stockAmount = productDetails.stock_amount || '0';
			var stockAmountOpened = productDetails.stock_amount_opened || '0';
			$('#productcard-product-name').text(productDetails.product.name);
			$('#productcard-product-stock-amount').text(stockAmount);
			$('#productcard-product-stock-qu-name').text(productDetails.quantity_unit_stock.name);
			$('#productcard-product-stock-qu-name2').text(Pluralize(stockAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural));
			$('#productcard-product-last-purchased').text((productDetails.last_purchased || L('never')).substring(0, 10));
			$('#productcard-product-last-purchased-timeago').text($.timeago(productDetails.last_purchased || ''));
			$('#productcard-product-last-used').text((productDetails.last_used || L('never')).substring(0, 10));
			$('#productcard-product-last-used-timeago').text($.timeago(productDetails.last_used || ''));

			if (stockAmountOpened > 0)
			{
				$('#productcard-product-stock-opened-amount').text(L('#1 opened', stockAmountOpened));
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
				$('#productcard-product-last-price').text(L('Unknown'));
			}

			if (productDetails.product.picture_file_name !== null && !productDetails.product.picture_file_name.isEmpty())
			{
				$("#productcard-no-product-picture").addClass("d-none");
				$("#productcard-product-picture").removeClass("d-none");
				$("#productcard-product-picture").attr("src", U('/api/file/productpictures?file_name=' + productDetails.product.picture_file_name));
			}
			else
			{
				$("#productcard-no-product-picture").removeClass("d-none");
				$("#productcard-product-picture").addClass("d-none");
			}

			EmptyElementWhenMatches('#productcard-product-last-purchased-timeago', L('timeago_nan'));
			EmptyElementWhenMatches('#productcard-product-last-used-timeago', L('timeago_nan'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);

	Grocy.Api.Get('stock/get-product-price-history/' + productId,
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
				borderColor: '#17a2b8'
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
