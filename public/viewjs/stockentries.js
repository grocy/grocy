var stockEntriesTable = $('#stockentries-table').DataTable({
	'order': [[2, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 10 },
		{ "type": "num", "targets": 1 },
		{ "type": "custom-sort", "targets": 3 },
		{ "type": "html", "targets": 4 },
		{ "type": "custom-sort", "targets": 7 },
		{ "type": "html", "targets": 8 },
		{ "type": "html", "targets": 9 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#stockentries-table tbody').removeClass("d-none");
stockEntriesTable.columns.adjust().draw();

$.fn.dataTable.ext.search.push(function(settings, data, dataIndex)
{
	var productId = Grocy.Components.ProductPicker.GetValue();

	if ((isNaN(productId) || productId == "" || productId == data[1]))
	{
		return true;
	}

	return false;
});

$("#clear-filter-button").on("click", function()
{
	$("#location-filter").val("all");
	$("#location-filter").trigger("change");
	Grocy.Components.ProductPicker.Clear();
	stockEntriesTable.draw();
});

$("#location-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#location-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	stockEntriesTable.column(stockEntriesTable.colReorder.transpose(5)).search(text).draw();
});

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	stockEntriesTable.draw();
});

Grocy.Components.ProductPicker.GetInputElement().on('keyup', function(e)
{
	stockEntriesTable.draw();
});

$(document).on('click', '.stock-consume-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var productId = $(e.currentTarget).attr('data-product-id');
	var locationId = $(e.currentTarget).attr('data-location-id');
	var specificStockEntryId = $(e.currentTarget).attr('data-stock-id');
	var stockRowId = $(e.currentTarget).attr('data-stockrow-id');
	var consumeAmount = $(e.currentTarget).attr('data-consume-amount');

	var wasSpoiled = $(e.currentTarget).hasClass("stock-consume-button-spoiled");

	Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': wasSpoiled, 'location_id': locationId, 'stock_entry_id': specificStockEntryId, 'exact_amount': true },
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					var toastMessage = __t('Removed %1$s of %2$s from stock', parseFloat(consumeAmount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural, true), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBookingEntry(' + bookingResponse[0].id + ',' + stockRowId + ')"><i class="fa-solid fa-undo"></i> ' + __t("Undo") + '</a>';
					if (wasSpoiled)
					{
						toastMessage += " (" + __t("Spoiled") + ")";
					}

					Grocy.FrontendHelpers.EndUiBusy();
					RefreshStockEntryRow(stockRowId);
					toastr.success(toastMessage);
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
	var specificStockEntryId = $(e.currentTarget).attr('data-stock-id');
	var stockRowId = $(e.currentTarget).attr('data-stockrow-id');
	var button = $(e.currentTarget);

	Grocy.Api.Post('stock/products/' + productId + '/open', { 'amount': 1, 'stock_entry_id': specificStockEntryId },
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					button.addClass("disabled");
					Grocy.FrontendHelpers.EndUiBusy();
					toastr.success(__t('Marked %1$s of %2$s as opened', 1 + " " + productQuName, productName) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBookingEntry(' + bookingResponse[0].id + ',' + stockRowId + ')"><i class="fa-solid fa-undo"></i> ' + __t("Undo") + '</a>');

					if (result.product.move_on_open == 1 && result.default_consume_location != null)
					{
						toastr.info('<span>' + __t("Moved to %1$s", result.default_consume_location.name) + "</span> <i class='fa-solid fa-exchange-alt'></i>");
					}

					RefreshStockEntryRow(stockRowId);
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

$(document).on("click", ".stock-name-cell", function(e)
{
	Grocy.Components.ProductCard.Refresh($(e.currentTarget).attr("data-stock-id"));
	$("#stockentry-productcard-modal").modal("show");
});

$(document).on('click', '.stockentry-grocycode-label-print', function(e)
{
	e.preventDefault();
	document.activeElement.blur();

	var stockId = $(e.currentTarget).attr('data-stock-id');
	Grocy.Api.Get('stock/entry/' + stockId + '/printlabel', function(labelData)
	{
		if (Grocy.Webhooks.labelprinter !== undefined)
		{
			Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, labelData);
		}
	});
});

function RefreshStockEntryRow(stockRowId)
{
	Grocy.Api.Get("stock/entry/" + stockRowId,
		function(result)
		{
			var stockRow = $('#stock-' + stockRowId + '-row');

			// If the stock row not exists / is invisible (happens after consume/undo because the undone new stock row has different id), just reload the page for now
			if (!stockRow.length || stockRow.hasClass("d-none"))
			{
				window.location.reload();
			}

			if (result == null || result.amount == 0)
			{
				animateCSS("#stock-" + stockRowId + "-row", "fadeOut", function()
				{
					$("#stock-" + stockRowId + "-row").addClass("d-none");
				});
			}
			else
			{
				var dueThreshold = moment().add(Grocy.UserSettings.stock_due_soon_days, "days");
				var now = moment();
				var bestBeforeDate = moment(result.best_before_date);

				stockRow.removeClass("table-warning");
				stockRow.removeClass("table-danger");
				stockRow.removeClass("table-info");
				stockRow.removeClass("d-none");
				stockRow.removeAttr("style");
				if (now.isAfter(bestBeforeDate))
				{
					if (stockRow.attr("data-due-type") == 1)
					{
						stockRow.addClass("table-secondary");
					}
					else
					{
						stockRow.addClass("table-danger");
					}
				}
				else if (bestBeforeDate.isBefore(dueThreshold))
				{
					stockRow.addClass("table-warning");
				}

				animateCSS("#stock-" + stockRowId + "-row td:not(:first)", "shake");

				$('#stock-' + stockRowId + '-amount').text(result.amount);
				$('#stock-' + stockRowId + '-due-date').text(result.best_before_date);
				$('#stock-' + stockRowId + '-due-date-timeago').attr('datetime', result.best_before_date + ' 23:59:59');

				$(".stock-consume-button").attr('data-location-id', result.location_id);

				var locationName = "";
				Grocy.Api.Get("objects/locations/" + result.location_id,
					function(locationResult)
					{
						locationName = locationResult.name;

						$('#stock-' + stockRowId + '-location').attr('data-location-id', result.location_id);
						$('#stock-' + stockRowId + '-location').text(locationName);
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);

				Grocy.Api.Get("stock/products/" + result.product_id,
					function(productDetails)
					{
						if (result.price == null || result.price.isEmpty())
						{
							result.price = 0;
						}

						$('#stock-' + stockRowId + '-price').text(__t("%1$s per %2$s", (Number.parseFloat(result.price) * Number.parseFloat(productDetails.product.qu_factor_purchase_to_stock)).toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }), productDetails.default_quantity_unit_purchase.name));
						$('#stock-' + stockRowId + '-price').attr("data-original-title", __t("%1$s per %2$s", Number.parseFloat(result.price).toLocaleString(undefined, { style: "currency", currency: Grocy.Currency, minimumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_prices_display }), productDetails.quantity_unit_stock.name));
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);

				$('#stock-' + stockRowId + '-note').text(result.note);
				$('#stock-' + stockRowId + '-purchased-date').text(result.purchased_date);
				$('#stock-' + stockRowId + '-purchased-date-timeago').attr('datetime', result.purchased_date + ' 23:59:59');

				if (result.shopping_location_id != null && !result.shopping_location_id.isEmpty())
				{
					var shoppingLocationName = "";
					Grocy.Api.Get("objects/shopping_locations/" + result.shopping_location_id,
						function(shoppingLocationResult)
						{
							shoppingLocationName = shoppingLocationResult.name;

							$('#stock-' + stockRowId + '-shopping-location').attr('data-shopping-location-id', result.location_id);
							$('#stock-' + stockRowId + '-shopping-location').text(shoppingLocationName);
						},
						function(xhr)
						{
							console.error(xhr);
						}
					);
				}
				else
				{
					$('#stock-' + stockRowId + '-shopping-location').text("");
				}

				if (result.open == 1)
				{
					$('#stock-' + stockRowId + '-opened-amount').text(__t('Opened'));
				}
				else
				{
					$('#stock-' + stockRowId + '-opened-amount').text("");
					$(".product-open-button[data-stockrow-id='" + stockRowId + "']").removeClass("disabled");
				}
			}

			// Needs to be delayed because of the animation above the date-text would be wrong if fired immediately...
			setTimeout(function()
			{
				RefreshContextualTimeago("#stock-" + stockRowId + "-row");
				RefreshLocaleNumberDisplay("#stock-" + stockRowId + "-row");
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

	if (data.Message === "StockEntryChanged")
	{
		RefreshStockEntryRow(data.Payload);
	}
});

Grocy.Components.ProductPicker.GetPicker().trigger('change');

function UndoStockBookingEntry(bookingId, stockRowId)
{
	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
		function(result)
		{
			window.postMessage(WindowMessageBag("StockEntryChanged", stockRowId), Grocy.BaseUrl);
			toastr.success(__t("Booking successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};

$(document).on("click", ".product-name-cell", function(e)
{
	Grocy.Components.ProductCard.Refresh($(e.currentTarget).attr("data-product-id"));
	$("#productcard-modal").modal("show");
});
