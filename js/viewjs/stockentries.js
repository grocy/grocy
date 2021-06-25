function stockentriesView(Grocy, scope = null)
{
	var $scope = $;
	var top = scope != null ? $(scope) : $(document);

	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	var productcard = Grocy.Use("productcard");
	var productpicker = Grocy.Use("productpicker");

	// preload some views.
	Grocy.PreloadView("stockentryform");
	Grocy.PreloadView("shoppinglistitemform");
	Grocy.PreloadView("purchase");
	Grocy.PreloadView("consume");
	Grocy.PreloadView("inventory");
	Grocy.PreloadView("stockjournal");
	Grocy.PreloadView("stockjournalsummary");

	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
		Grocy.PreloadView("transfer");


	var stockEntriesTable = $scope('#stockentries-table').DataTable({
		'order': [[2, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#stockentries-table tbody').removeClass("d-none");
	stockEntriesTable.columns.adjust().draw();

	var moreSearch = function(settings, data, dataIndex)
	{
		var productId = productpicker.GetValue();

		if ((isNaN(productId) || productId == "" || productId == data[1]))
		{
			return true;
		}

		return false;
	};

	$.fn.dataTable.ext.search.push(moreSearch);


	Grocy.RegisterUnload(() =>
	{
		var funcIdx = $.fn.dataTable.ext.search.indexOf(moreSearch);
		if (funcIdx !== -1)
			$.fn.dataTable.ext.search.splice(funcIdx);
	});

	$scope("#clear-filter-button").on("click", function()
	{
		productpicker.Clear();
		stockEntriesTable.draw();
	});

	productpicker.GetPicker().on('change', function(e)
	{
		stockEntriesTable.draw();
	});

	productpicker.GetInputElement().on('keyup', function(e)
	{
		stockEntriesTable.draw();
	});

	top.on('click', '.stock-consume-button', function(e)
	{
		e.preventDefault();

		// Remove the focus from the current button
		// to prevent that the tooltip stays until clicked anywhere else
		document.activeElement.blur();

		Grocy.FrontendHelpers.BeginUiBusy();


		var target = $(e.currentTarget);
		var productId = target.attr('data-product-id');
		var locationId = target.attr('data-location-id');
		var specificStockEntryId = target.attr('data-stock-id');
		var stockRowId = target.attr('data-stockrow-id');
		var consumeAmount = target.attr('data-consume-amount');

		var wasSpoiled = target.hasClass("stock-consume-button-spoiled");

		Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': wasSpoiled, 'location_id': locationId, 'stock_entry_id': specificStockEntryId, 'exact_amount': true },
			function(bookingResponse)
			{
				Grocy.Api.Get('stock/products/' + productId,
					function(result)
					{
						var toastMessage = __t('Removed %1$s of %2$s from stock', parseFloat(consumeAmount).toLocaleString({ minimumFractionDigits: 0, maximumFractionDigits: Grocy.UserSettings.stock_decimal_places_amounts }) + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoStockBookingEntry(' + bookingResponse.id + ',' + stockRowId + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
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

	top.on('click', '.product-open-button', function(e)
	{
		e.preventDefault();

		// Remove the focus from the current button
		// to prevent that the tooltip stays until clicked anywhere else
		document.activeElement.blur();

		Grocy.FrontendHelpers.BeginUiBusy();

		var button = $(e.currentTarget)
		var productId = button.attr('data-product-id');
		var productName = button.attr('data-product-name');
		var productQuName = button.attr('data-product-qu-name');
		var specificStockEntryId = button.attr('data-stock-id');
		var stockRowId = button.attr('data-stockrow-id');


		Grocy.Api.Post('stock/products/' + productId + '/open', { 'amount': 1, 'stock_entry_id': specificStockEntryId },
			function(bookingResponse)
			{
				button.addClass("disabled");
				Grocy.FrontendHelpers.EndUiBusy();
				toastr.success(__t('Marked %1$s of %2$s as opened', 1 + " " + productQuName, productName) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="Grocy.UndoStockBookingEntry(' + bookingResponse.id + ',' + stockRowId + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');
				RefreshStockEntryRow(stockRowId);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy();
				console.error(xhr);
			}
		);
	});

	top.on("click", ".stock-name-cell", function(e)
	{
		productcard.Refresh($(e.currentTarget).attr("data-stock-id"));
		$scope("#stockentry-productcard-modal").modal("show");
	});

	top.on('click', '.stockentry-grocycode-stockentry-label-print', function(e)
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
				var stockRow = $scope('#stock-' + stockRowId + '-row');

				// If the stock row not exists / is invisible (happens after consume/undo because the undone new stock row has different id), just reload the page for now
				if (!stockRow.length || stockRow.hasClass("d-none"))
				{
					window.location.reload();
				}

				if (result == null || result.amount == 0)
				{
					animateCSS("#stock-" + stockRowId + "-row", "fadeOut", function()
					{
						$scope("#stock-" + stockRowId + "-row").addClass("d-none");
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

					$scope('#stock-' + stockRowId + '-amount').text(result.amount);
					$scope('#stock-' + stockRowId + '-due-date').text(result.best_before_date);
					$scope('#stock-' + stockRowId + '-due-date-timeago').attr('datetime', result.best_before_date + ' 23:59:59');

					$scope(".stock-consume-button").attr('data-location-id', result.location_id);

					var locationName = "";
					Grocy.Api.Get("objects/locations/" + result.location_id,
						function(locationResult)
						{
							locationName = locationResult.name;

							$scope('#stock-' + stockRowId + '-location').attr('data-location-id', result.location_id);
							$scope('#stock-' + stockRowId + '-location').text(locationName);
						},
						function(xhr)
						{
							console.error(xhr);
						}
					);

					$scope('#stock-' + stockRowId + '-price').text(result.price);
					$scope('#stock-' + stockRowId + '-purchased-date').text(result.purchased_date);
					$scope('#stock-' + stockRowId + '-purchased-date-timeago').attr('datetime', result.purchased_date + ' 23:59:59');

					var shoppingLocationName = "";
					Grocy.Api.Get("objects/shopping_locations/" + result.shopping_location_id,
						function(shoppingLocationResult)
						{
							shoppingLocationName = shoppingLocationResult.name;

							$scope('#stock-' + stockRowId + '-shopping-location').attr('data-shopping-location-id', result.location_id);
							$scope('#stock-' + stockRowId + '-shopping-location').text(shoppingLocationName);
						},
						function(xhr)
						{
							console.error(xhr);
						}
					);

					if (result.open == 1)
					{
						$scope('#stock-' + stockRowId + '-opened-amount').text(__t('Opened'));
					}
					else
					{
						$scope('#stock-' + stockRowId + '-opened-amount').text("");
						$scope(".product-open-button[data-stockrow-id='" + stockRowId + "']").removeClass("disabled");
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

	productpicker.GetPicker().trigger('change');

	top.on("click", ".product-name-cell", function(e)
	{
		productcard.Refresh($(e.currentTarget).attr("data-product-id"));
		$scope("#productcard-modal").modal("show");
	});

}


window.stockentriesView = stockentriesView
