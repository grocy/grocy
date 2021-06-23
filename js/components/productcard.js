import Chart from 'chart.js';
import { EmptyElementWhenMatches } from '../helpers/extensions'
import { RefreshContextualTimeago } from '../configs/timeago'

class productcard
{
	constructor(Grocy, scopeSelector = null)
	{
		this.Grocy = Grocy;

		this.scopeSelector = scopeSelector;
		this.scope = scopeSelector != null ? $(scope) : $(document);
		this.$ = scopeSelector != null ? $(scope).find : $;
		this.PriceHistoryChart = null;
		var self = this;

		this.$("#productcard-product-description").on("shown.bs.collapse", function()
		{
			self.$(".expandable-text")
				.find("a[data-toggle='collapse']")
				.text(self.Grocy.translate("Show less"));
		})

		this.$("#productcard-product-description").on("hidden.bs.collapse", function()
		{
			self.$(".expandable-text")
				.find("a[data-toggle='collapse']")
				.text(self.Grocy.translate("Show more"));
		})
	}

	Refresh(productId)
	{
		var self = this;
		this.Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				var stockAmount = productDetails.stock_amount || '0';
				var stockValue = productDetails.stock_value || '0';
				var stockAmountOpened = productDetails.stock_amount_opened || '0';
				self.$('#productcard-product-name').text(productDetails.product.name);
				self.$('#productcard-product-description').html(productDetails.product.description);
				self.$('#productcard-product-stock-amount').text(stockAmount);
				self.$('#productcard-product-stock-qu-name').text(self.Grocy.translaten(stockAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural));
				self.$('#productcard-product-stock-value').text(stockValue + ' ' + self.Grocy.Currency);
				self.$('#productcard-product-last-purchased').text((productDetails.last_purchased || '2999-12-31').substring(0, 10));
				self.$('#productcard-product-last-purchased-timeago').attr("datetime", productDetails.last_purchased || '2999-12-31');
				self.$('#productcard-product-last-used').text((productDetails.last_used || '2999-12-31').substring(0, 10));
				self.$('#productcard-product-last-used-timeago').attr("datetime", productDetails.last_used || '2999-12-31');
				if (productDetails.location != null)
				{
					self.$('#productcard-product-location').text(productDetails.location.name);
				}
				self.$('#productcard-product-spoil-rate').text((parseFloat(productDetails.spoil_rate_percent) / 100).toLocaleString(undefined, { style: "percent" }));

				if (productDetails.is_aggregated_amount == 1)
				{
					self.$('#productcard-product-stock-amount-aggregated').text(productDetails.stock_amount_aggregated);
					self.$('#productcard-product-stock-qu-name-aggregated').text(self.Grocy.translaten(productDetails.stock_amount_aggregated, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural));

					if (productDetails.stock_amount_opened_aggregated > 0)
					{
						self.$('#productcard-product-stock-opened-amount-aggregated').text(self.Grocy.translate('%s opened', productDetails.stock_amount_opened_aggregated));
					}
					else
					{
						self.$('#productcard-product-stock-opened-amount-aggregated').text("");
					}

					self.$("#productcard-aggregated-amounts").removeClass("d-none");
				}
				else
				{
					self.$("#productcard-aggregated-amounts").addClass("d-none");
				}

				if (productDetails.product.description != null && !productDetails.product.description.isEmpty())
				{
					self.$("#productcard-product-description-wrapper").removeClass("d-none");
				}
				else
				{
					self.$("#productcard-product-description-wrapper").addClass("d-none");
				}

				if (productDetails.average_shelf_life_days == -1)
				{
					self.$('#productcard-product-average-shelf-life').text(self.Grocy.translate("Unknown"));
				}
				else if (parseInt(productDetails.average_shelf_life_days) > 73000) // > 200 years aka forever
				{
					self.$('#productcard-product-average-shelf-life').text(self.Grocy.translate("Unlimited"));
				}
				else
				{
					self.$('#productcard-product-average-shelf-life').text(moment.duration(productDetails.average_shelf_life_days, "days").humanize());
				}

				if (stockAmountOpened > 0)
				{
					self.$('#productcard-product-stock-opened-amount').text(self.Grocy.translate('%s opened', stockAmountOpened));
				}
				else
				{
					self.$('#productcard-product-stock-opened-amount').text("");
				}

				self.$('#productcard-product-edit-button').attr("href", self.Grocy.FormatUrl("/product/" + productDetails.product.id.toString() + '?' + 'returnto=' + encodeURIComponent(self.Grocy.CurrentUrlRelative)));
				self.$('#productcard-product-journal-button').attr("href", self.Grocy.FormatUrl("/stockjournal?embedded&product=" + productDetails.product.id.toString()));
				self.$('#productcard-product-stock-button').attr("href", self.Grocy.FormatUrl("/stockentries?embedded&product=" + productDetails.product.id.toString()));
				self.$('#productcard-product-stock-button').removeClass("disabled");
				self.$('#productcard-product-edit-button').removeClass("disabled");
				self.$('#productcard-product-journal-button').removeClass("disabled");

				if (productDetails.last_price !== null)
				{
					self.$('#productcard-product-last-price').text(Number.parseFloat(productDetails.last_price).toLocaleString() + ' ' + self.Grocy.Currency + ' per ' + productDetails.quantity_unit_stock.name);
				}
				else
				{
					self.$('#productcard-product-last-price').text(self.Grocy.translate('Unknown'));
				}

				if (productDetails.avg_price !== null)
				{
					self.$('#productcard-product-average-price').text(Number.parseFloat(productDetails.avg_price).toLocaleString() + ' ' + self.Grocy.Currency + ' per ' + productDetails.quantity_unit_stock.name);
				}
				else
				{
					self.$('#productcard-product-average-price').text(self.Grocy.translate('Unknown'));
				}

				if (productDetails.product.picture_file_name !== null && !productDetails.product.picture_file_name.isEmpty())
				{
					self.$("#productcard-product-picture").removeClass("d-none");
					self.$("#productcard-product-picture").attr("src", Grocy.FormatUrl('/api/files/productpictures/' + btoa(productDetails.product.picture_file_name) + '?force_serve_as=picture&best_fit_width=400'));
				}
				else
				{
					self.$("#productcard-product-picture").addClass("d-none");
				}

				EmptyElementWhenMatches(self.$('#productcard-product-last-purchased-timeago'), self.Grocy.translate('timeago_nan'));
				EmptyElementWhenMatches(self.$('#productcard-product-last-used-timeago'), self.Grocy.translate('timeago_nan'));
				RefreshContextualTimeago(".productcard");
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);

		if (this.Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
		{
			this.Grocy.Api.Get('stock/products/' + productId + '/price-history',
				function(priceHistoryDataPoints)
				{
					if (priceHistoryDataPoints.length > 0)
					{
						self.$("#productcard-product-price-history-chart").removeClass("d-none");
						self.$("#productcard-no-price-data-hint").addClass("d-none");

						self.ReInitPriceHistoryChart();
						var datasets = {};
						var chart = self.PriceHistoryChart.data;
						for (let dataPoint of priceHistoryDataPoints)
						{
							let key = Grocy.translate("Unknown store");
							if (dataPoint.shopping_location)
							{
								key = dataPoint.shopping_location.name
							}

							if (!datasets[key])
							{
								datasets[key] = []
							}
							chart.labels.push(moment(dataPoint.date).toDate());
							datasets[key].push(dataPoint.price);
						}

						for (let key of Object.keys(datasets))
						{
							chart.datasets.push({
								data: datasets[key],
								fill: false,
								borderColor: "HSL(" + (129 * chart.datasets.length) + ",100%,50%)",
								label: key,
							});
						}
						self.PriceHistoryChart.update();
					}
					else
					{
						self.$("#productcard-product-price-history-chart").addClass("d-none");
						self.$("#productcard-no-price-data-hint").removeClass("d-none");
					}
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	}

	ReInitPriceHistoryChart()
	{
		if (this.PriceHistoryChart !== null)
		{
			this.PriceHistoryChart.destroy();
		}

		var format = 'YYYY-MM-DD';
		this.PriceHistoryChart = new Chart(this.$("#productcard-product-price-history-chart")[0], {
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
							beginAtZero: true
						}
					}]
				},
				legend: {
					display: true
				}
			}
		});
	}
}

export { productcard }