var stockOverviewTable = $('#stock-overview-table').DataTable({
	'paginate': false,
	'order': [[3, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'visible': false, 'targets': 4 },
		{ 'visible': false, 'targets': 5 },
		{ 'visible': false, 'targets': 6 }
	],
	'language': JSON.parse(__t('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	}
});
$('#stock-overview-table tbody').removeClass("d-none");
stockOverviewTable.columns.adjust().draw();

$("#location-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	stockOverviewTable.column(4).search(value).draw();
});

$("#product-group-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	stockOverviewTable.column(6).search(value).draw();
});

$("#status-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	// Transfer CSS classes of selected element to dropdown element (for background)
	$(this).attr("class", $("#" + $(this).attr("id") + " option[value='" + value + "']").attr("class") + " form-control");

	stockOverviewTable.column(5).search(value).draw();
});

$(".status-filter-button").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	stockOverviewTable.search(value).draw();
});

$(document).on('click', '.product-consume-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var productId = $(e.currentTarget).attr('data-product-id');
	var consumeAmount = $(e.currentTarget).attr('data-consume-amount');
	var wasSpoiled = $(e.currentTarget).hasClass("product-consume-button-spoiled");

	Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': wasSpoiled },
		function()
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					var productRow = $('#product-' + productId + '-row');
					var expiringThreshold = moment().add("-" + $("#info-expiring-products").data("next-x-days"), "days");
					var now = moment();
					var nextBestBeforeDate = moment(result.next_best_before_date);

					productRow.removeClass("table-warning");
					productRow.removeClass("table-danger");
					if (now.isAfter(nextBestBeforeDate))
					{
						productRow.addClass("table-danger");
					}
					if (expiringThreshold.isAfter(nextBestBeforeDate))
					{
						productRow.addClass("table-warning");
					}

					var oldAmount = parseFloat($('#product-' + productId + '-amount').text());
					var newAmount = oldAmount - consumeAmount;
					if (newAmount <= 0) // When "consume all" of an amount < 1, the resulting amount here will be < 0, but the API newer books > current stock amount
					{
						$('#product-' + productId + '-row').fadeOut(500, function()
						{
							$(this).tooltip("hide");
							$(this).remove();
						});
					}
					else
					{
						$('#product-' + productId + '-qu-name').text(__n(newAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural));
						$('#product-' + productId + '-amount').parent().effect('highlight', { }, 500);
						$('#product-' + productId + '-amount').fadeOut(500, function ()
						{
							$(this).text(newAmount).fadeIn(500);
						});
						$('#product-' + productId + '-consume-all-button').attr('data-consume-amount', newAmount);

						$('#product-' + productId + '-next-best-before-date').parent().effect('highlight', { }, 500);
						$('#product-' + productId + '-next-best-before-date').fadeOut(500, function()
						{
							$(this).text(result.next_best_before_date).fadeIn(500);
						});
						$('#product-' + productId + '-next-best-before-date-timeago').attr('datetime', result.next_best_before_date);

						var openedAmount = result.stock_amount_opened || 0;
						$('#product-' + productId + '-opened-amount').parent().effect('highlight', {}, 500);
						$('#product-' + productId + '-opened-amount').fadeOut(500, function ()
						{
							if (openedAmount > 0)
							{
								$(this).text(__t('%s opened', openedAmount)).fadeIn(500);
							}
							else
							{
								$(this).text("").fadeIn(500);
							}
						});
					}

					var toastMessage = __t('Removed %1$s of %2$s from stock', consumeAmount.toString() + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name);
					if (wasSpoiled)
					{
						toastMessage += " (" + __t("Spoiled") + ")";
					}

					Grocy.FrontendHelpers.EndUiBusy();
					toastr.success(toastMessage);
					RefreshStatistics();

					// Needs to be delayed because of the animation above the date-text would be wrong if fired immediately...
					setTimeout(function ()
					{
						RefreshContextualTimeago();
					}, 520);
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy();
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
			console.error(xhr);
		}
	);
});

$(document).on('click', '.product-open-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var productId = $(e.currentTarget).attr('data-product-id');
	var productName = $(e.currentTarget).attr('data-product-name');
	var productQuName = $(e.currentTarget).attr('data-product-qu-name');
	var button = $(e.currentTarget);

	Grocy.Api.Post('stock/products/' + productId + '/open', { 'amount': 1 },
		function()
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					var productRow = $('#product-' + productId + '-row');
					var expiringThreshold = moment().add("-" + $("#info-expiring-products").data("next-x-days"), "days");
					var now = moment();
					var nextBestBeforeDate = moment(result.next_best_before_date);

					productRow.removeClass("table-warning");
					productRow.removeClass("table-danger");
					if (now.isAfter(nextBestBeforeDate))
					{
						productRow.addClass("table-danger");
					}
					if (expiringThreshold.isAfter(nextBestBeforeDate))
					{
						productRow.addClass("table-warning");
					}

					$('#product-' + productId + '-next-best-before-date').parent().effect('highlight', {}, 500);
					$('#product-' + productId + '-next-best-before-date').fadeOut(500, function()
					{
						$(this).text(result.next_best_before_date).fadeIn(500);
					});
					$('#product-' + productId + '-next-best-before-date-timeago').attr('datetime', result.next_best_before_date);

					$('#product-' + productId + '-opened-amount').parent().effect('highlight', {}, 500);
					$('#product-' + productId + '-opened-amount').fadeOut(500, function()
					{
						$(this).text(__t('%s opened', result.stock_amount_opened)).fadeIn(500);
					});

					if (result.stock_amount == result.stock_amount_opened)
					{
						button.addClass("disabled");
					}

					Grocy.FrontendHelpers.EndUiBusy();
					toastr.success(__t('Marked %1$s of %2$s as opened', 1 + " " + productQuName, productName));
					RefreshStatistics();

					// Needs to be delayed because of the animation above the date-text would be wrong if fired immediately...
					setTimeout(function()
					{
						RefreshContextualTimeago();
					}, 600);
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy();
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
			console.error(xhr);
		}
	);
});

$(document).on("click", ".product-name-cell", function(e)
{
	Grocy.Components.ProductCard.Refresh($(e.currentTarget).attr("data-product-id"));
	$("#stockoverview-productcard-modal").modal("show");
});

function RefreshStatistics()
{
	Grocy.Api.Get('stock',
		function(result)
		{
			var amountSum = 0;
			result.forEach(element => {
				amountSum += parseInt(element.amount);
			});
			$("#info-current-stock").text(__n(result.length, '%s Product', '%s Products') + ", " + __n(amountSum, '%s Unit', '%s Units'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);

	var nextXDays = $("#info-expiring-products").data("next-x-days");
	Grocy.Api.Get('stock/volatile?expiring_days=' + nextXDays,
		function(result)
		{
			$("#info-expiring-products").text(__n(result.expiring_products.length, '%s product expires', '%s products expiring') + ' ' + __n(nextXDays, 'within the next day', 'within the next %s days'));
			$("#info-expired-products").text(__n(result.expired_products.length, '%s product is already expired', '%s products are already expired'));
			$("#info-missing-products").text(__n(result.missing_products.length, '%s product is below defined min. stock amount', '%s products are below defined min. stock amount'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

RefreshStatistics();
