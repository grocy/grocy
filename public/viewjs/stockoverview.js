var stockOverviewTable = $('#stock-overview-table').DataTable({
	'order': [[3, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 4 },
		{ 'visible': false, 'targets': 5 },
		{ 'visible': false, 'targets': 6 }
	],
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

$(".status-filter-message").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
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

	Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': wasSpoiled },
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					if (result.product.enable_tare_weight_handling == 1)
					{
						var toastMessage = __t('Removed %1$s of %2$s from stock', parseFloat(originalTotalStockAmount).toString() + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					}
					else
					{
						var toastMessage = __t('Removed %1$s of %2$s from stock', consumeAmount.toString() + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
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
	var button = $(e.currentTarget);

	Grocy.Api.Post('stock/products/' + productId + '/open', { 'amount': 1 },
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
					toastr.success(__t('Marked %1$s of %2$s as opened', 1 + " " + productQuName, productName) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');
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
			result.forEach(element => {
				amountSum += parseInt(element.amount);
			});
			$("#info-current-stock").text(__n(result.length, '%s Product', '%s Products'));
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
			var expiringThreshold = moment().add($("#info-expiring-products").data("next-x-days"), "days");
			var now = moment();
			var nextBestBeforeDate = moment(result.next_best_before_date);
			
			productRow.removeClass("table-warning");
			productRow.removeClass("table-danger");
			productRow.removeClass("table-info");
			productRow.removeClass("d-none");
			productRow.removeAttr("style");
			if (now.isAfter(nextBestBeforeDate))
			{
				productRow.addClass("table-danger");
			}
			else if (nextBestBeforeDate.isBefore(expiringThreshold))
			{
				productRow.addClass("table-warning");
			}

			if (result.stock_amount == 0 && result.product.min_stock_amount == 0)
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

				$('#product-' + productId + '-next-best-before-date').text(result.next_best_before_date);
				$('#product-' + productId + '-next-best-before-date-timeago').attr('datetime', result.next_best_before_date);

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

			$('#product-' + productId + '-next-best-before-date').text(result.next_best_before_date);
			$('#product-' + productId + '-next-best-before-date-timeago').attr('datetime', result.next_best_before_date + ' 23:59:59');

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
