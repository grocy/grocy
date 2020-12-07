var stockOverviewTable = $('#stock-overview-table').DataTable({
	'order': [[4, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 6 },
		{ 'visible': false, 'targets': 7 },
		{ 'visible': false, 'targets': 8 },
		{ 'visible': false, 'targets': 2 },
		{ 'visible': false, 'targets': 4 },
		{ 'visible': false, 'targets': 9 },
		{ 'visible': false, 'targets': 10 }
	].concat($.fn.dataTable.defaults.columnDefs)
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
	else
	{
		value = "xx" + value + "xx";
	}

	stockOverviewTable.column(6).search(value).draw();
});

$("#product-group-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	else
	{
		value = "xx" + value + "xx";
	}

	stockOverviewTable.column(8).search(value).draw();
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

	stockOverviewTable.column(7).search(value).draw();
});

$(".status-filter-message").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
});

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#status-filter").val("all");
	$("#product-group-filter").val("all");
	$("#location-filter").val("all");
	stockOverviewTable.column(6).search("").draw();
	stockOverviewTable.column(7).search("").draw();
	stockOverviewTable.column(8).search("").draw();
	stockOverviewTable.search("").draw();
});

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	stockOverviewTable.search(value).draw();
}, 200));

$(document).on('click', '.product-consume-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var productId = $(e.currentTarget).attr('data-product-id');
	var consumeAmount = $(e.currentTarget).attr('data-consume-amount');
	var originalTotalStockAmount = $(e.currentTarget).attr('data-original-total-stock-amount');
	var wasSpoiled = $(e.currentTarget).hasClass("product-consume-button-spoiled");

	Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': wasSpoiled, 'allow_subproduct_substitution': true },
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					if (result.product.enable_tare_weight_handling == 1)
					{
						var toastMessage = __t('Removed %1$s of %2$s from stock', parseFloat(originalTotalStockAmount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					}
					else
					{
						var toastMessage = __t('Removed %1$s of %2$s from stock', parseFloat(consumeAmount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					}

					if (wasSpoiled)
					{
						toastMessage += " (" + __t("Spoiled") + ")";
					}

					Grocy.FrontendHelpers.EndUiBusy();
					toastr.success(toastMessage);
					RefreshStatistics();
					RefreshProductRow(productId);
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
	var amount = $(e.currentTarget).attr('data-open-amount');
	var button = $(e.currentTarget);

	Grocy.Api.Post('stock/products/' + productId + '/open', { 'amount': amount, 'allow_subproduct_substitution': true },
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					if (result.stock_amount == result.stock_amount_opened)
					{
						button.addClass("disabled");
					}

					Grocy.FrontendHelpers.EndUiBusy();
					toastr.success(__t('Marked %1$s of %2$s as opened', parseFloat(amount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + productQuName, productName) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');
					RefreshStatistics();
					RefreshProductRow(productId);
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
			result.forEach(element =>
			{
				amountSum += parseInt(element.amount);
			});

			if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				$("#info-current-stock").text(__n(result.length, '%s Product', '%s Products'));
			}
			else
			{
				var valueSum = 0;
				result.forEach(element =>
				{
					valueSum += parseInt(element.value);
				});
				$("#info-current-stock").text(__n(result.length, '%s Product', '%s Products') + ", " + __t('%s total value', valueSum.toLocaleString(undefined, { style: "currency", currency: Grocy.Currency })));
			}
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);

	var nextXDays = $("#info-duesoon-products").data("next-x-days");
	Grocy.Api.Get('stock/volatile?due_soon_days=' + nextXDays,
		function(result)
		{
			$("#info-duesoon-products").html('<span class="d-block d-md-none">' + result.due_products.length + ' <i class="fas fa-clock"></i></span><span class="d-none d-md-block">' + __n(result.due_products.length, '%s product is due', '%s products are due') + ' ' + __n(nextXDays, 'within the next day', 'within the next %s days') + '</span>');
			$("#info-overdue-products").html('<span class="d-block d-md-none">' + result.overdue_products.length + ' <i class="fas fa-times-circle"></i></span><span class="d-none d-md-block">' + __n(result.overdue_products.length, '%s product is overdue', '%s products are overdue') + '</span>');
			$("#info-expired-products").html('<span class="d-block d-md-none">' + result.expired_products.length + ' <i class="fas fa-times-circle"></i></span><span class="d-none d-md-block">' + __n(result.expired_products.length, '%s product is expired', '%s products are expired') + '</span>');
			$("#info-missing-products").html('<span class="d-block d-md-none">' + result.missing_products.length + ' <i class="fas fa-exclamation-circle"></i></span><span class="d-none d-md-block">' + __n(result.missing_products.length, '%s product is below defined min. stock amount', '%s products are below defined min. stock amount') + '</span>');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}
RefreshStatistics();

function RefreshProductRow(productId)
{
	productId = productId.toString();

	Grocy.Api.Get('stock/products/' + productId,
		function(result)
		{
			// Also refresh the parent product, if any
			if (result.product.parent_product_id !== null && !result.product.parent_product_id.toString().isEmpty())
			{
				RefreshProductRow(result.product.parent_product_id);
			}

			var productRow = $('#product-' + productId + '-row');
			var dueSoonThreshold = moment().add($("#info-duesoon-products").data("next-x-days"), "days");
			var now = moment();
			var nextDueDate = moment(result.next_due_date);

			productRow.removeClass("table-warning");
			productRow.removeClass("table-danger");
			productRow.removeClass("table-secondary");
			productRow.removeClass("table-info");
			productRow.removeClass("d-none");
			productRow.removeAttr("style");
			if (now.isAfter(nextDueDate))
			{
				if (result.product.due_type == 1)
				{
					productRow.addClass("table-secondary");
				}
				else
				{
					productRow.addClass("table-danger");
				}
			}
			else if (nextDueDate.isBefore(dueSoonThreshold))
			{
				productRow.addClass("table-warning");
			}

			if (result.stock_amount == 0 && result.stock_amount_aggregated == 0 && result.product.min_stock_amount == 0)
			{
				animateCSS("#product-" + productId + "-row", "fadeOut", function()
				{
					$("#product-" + productId + "-row").tooltip("hide");
					$("#product-" + productId + "-row").addClass("d-none");
				});
			}
			else
			{
				animateCSS("#product-" + productId + "-row td:not(:first)", "shake");

				$('#product-' + productId + '-qu-name').text(__n(result.stock_amount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural));
				$('#product-' + productId + '-amount').text(result.stock_amount);
				$('#product-' + productId + '-consume-all-button').attr('data-consume-amount', result.stock_amount);
				$('#product-' + productId + '-value').text(result.stock_value);
				$('#product-' + productId + '-next-due-date').text(result.next_due_date);
				$('#product-' + productId + '-next-due-date-timeago').attr('datetime', result.next_due_date);

				var openedAmount = result.stock_amount_opened || 0;
				if (openedAmount > 0)
				{
					$('#product-' + productId + '-opened-amount').text(__t('%s opened', openedAmount));
				}
				else
				{
					$('#product-' + productId + '-opened-amount').text("");
				}

				if (result.stock_amount == 0 && result.product.min_stock_amount > 0)
				{
					productRow.addClass("table-info");
				}
			}

			$('#product-' + productId + '-next-due-date').text(result.next_due_date);
			$('#product-' + productId + '-next-due-date-timeago').attr('datetime', result.next_due_date + ' 23:59:59');

			if (result.stock_amount_opened > 0)
			{
				$('#product-' + productId + '-opened-amount').text(__t('%s opened', result.stock_amount_opened));
			}
			else
			{
				$('#product-' + productId + '-opened-amount').text("");
			}

			if (parseInt(result.is_aggregated_amount) === 1)
			{
				$('#product-' + productId + '-amount-aggregated').text(result.stock_amount_aggregated);

				if (result.stock_amount_opened_aggregated > 0)
				{
					$('#product-' + productId + '-opened-amount-aggregated').text(__t('%s opened', result.stock_amount_opened_aggregated));
				}
				else
				{
					$('#product-' + productId + '-opened-amount-aggregated').text("");
				}
			}

			// Needs to be delayed because of the animation above the date-text would be wrong if fired immediately...
			setTimeout(function()
			{
				RefreshContextualTimeago("#product-" + productId + "-row");
				RefreshLocaleNumberDisplay("#product-" + productId + "-row");
			}, 600);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
			console.error(xhr);
		}
	);
}

$(window).on("message", function(e)
{
	var data = e.originalEvent.data;

	if (data.Message === "ProductChanged")
	{
		RefreshProductRow(data.Payload);
		RefreshStatistics();
	}
});
