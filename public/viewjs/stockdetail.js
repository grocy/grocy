var stockDetailTable = $('#stock-detail-table').DataTable({
	'order': [[2, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	],
});
$('#stock-detail-table tbody').removeClass("d-none");

$.fn.dataTable.ext.search.push(function(settings, data, dataIndex)
{
	var productId = Grocy.Components.ProductPicker.GetValue();

	if ((isNaN(productId) || productId == "" || productId == data[1]))
	{
		return true;
	}
	
	return false;
});

Grocy.Components.ProductPicker.GetPicker().trigger('change');

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	stockDetailTable.draw();
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

	Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': wasSpoiled, 'location_id': locationId, 'stock_entry_id': specificStockEntryId},
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					var toastMessage = __t('Removed %1$s of %2$s from stock', consumeAmount.toString() + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBookingEntry(' + bookingResponse.id + ',' + stockRowId + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					if (wasSpoiled)
					{
						toastMessage += " (" + __t("Spoiled") + ")";
					}

					Grocy.FrontendHelpers.EndUiBusy();
					RefreshStockDetailRow(stockRowId);
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
			button.addClass("disabled");
			Grocy.FrontendHelpers.EndUiBusy();
			toastr.success(__t('Marked %1$s of %2$s as opened', 1 + " " + productQuName, productName) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBookingEntry(' + bookingResponse.id + ',' + stockRowId + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');
			RefreshStockDetailRow(stockRowId);
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
	$("#stockdetail-productcard-modal").modal("show");
});

function RefreshStockDetailRow(stockRowId)
{
	Grocy.Api.Get("stock/entry/" + stockRowId,
		function(result)
		{
			var stockRow = $('#stock-' + stockRowId + '-row');
			
			if (result == null || result.amount == 0)
			{
				stockRow.fadeOut(500, function()
				{
					$(this).addClass("d-none");
				});
			}
			else
			{
				var expiringThreshold = moment().add(Grocy.UserSettings.stock_expring_soon_days, "days");
				var now = moment();
				var bestBeforeDate = moment(result.best_before_date);

				stockRow.removeClass("table-warning");
				stockRow.removeClass("table-danger");
				stockRow.removeClass("table-info");
				stockRow.removeClass("d-none");
				stockRow.removeAttr("style");
				if (now.isAfter(bestBeforeDate))
				{
					stockRow.addClass("table-danger");
				}
				else if (bestBeforeDate.isBefore(expiringThreshold))
				{
					stockRow.addClass("table-warning");
				}

				$('#stock-' + stockRowId + '-amount').parent().effect('highlight', { }, 500);
				$('#stock-' + stockRowId + '-amount').fadeOut(500, function ()
				{
					$(this).text(result.amount).fadeIn(500);
				});

				$('#stock-' + stockRowId + '-best-before-date').parent().effect('highlight', { }, 500);
				$('#stock-' + stockRowId + '-best-before-date').fadeOut(500, function()
				{
					$(this).text(result.best_before_date).fadeIn(500);
				});
				$('#stock-' + stockRowId + '-best-before-date-timeago').attr('datetime', result.best_before_date + ' 23:59:59');

				var locationName = "";
				Grocy.Api.Get("objects/locations/" + result.location_id,
					function(locationResult)
					{
						locationName = locationResult.name;
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
				$('#stock-' + stockRowId + '-location').parent().effect('highlight', { }, 500);
				$('#stock-' + stockRowId + '-location').fadeOut(500, function()
				{
					$(this).text(locationName).fadeIn(500);
				});

				$('#stock-' + stockRowId + '-price').parent().effect('highlight', { }, 500);
				$('#stock-' + stockRowId + '-price').fadeOut(500, function()
				{
					$(this).text(result.price).fadeIn(500);
				});

				$('#stock-' + stockRowId + '-purchased-date').parent().effect('highlight', { }, 500);
				$('#stock-' + stockRowId + '-purchased-date').fadeOut(500, function()
				{
					$(this).text(result.purchased_date).fadeIn(500);
				});
				$('#stock-' + stockRowId + '-purchased-date-timeago').attr('datetime', result.purchased_date + ' 23:59:59');

				$('#stock-' + stockRowId + '-opened-amount').parent().effect('highlight', {}, 500);
				$('#stock-' + stockRowId + '-opened-amount').fadeOut(500, function ()
				{
					if (result.open == 1)
					{
						$(this).text(__t('Opened')).fadeIn(500);
					}
					else
					{
						$(this).text("").fadeIn(500);
						$(".product-open-button[data-stockrow-id='" + stockRowId + "']").removeClass("disabled");
					}
				});
			}

			// Needs to be delayed because of the animation above the date-text would be wrong if fired immediately...
			setTimeout(function()
			{
				RefreshContextualTimeago();
				RefreshLocaleNumberDisplay();
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

	if (data.Message === "StockDetailChanged")
	{
		RefreshStockDetailRow(data.Payload);
	}
});

function UndoStockBookingEntry(bookingId, stockRowId)
{
	Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', { },
		function(result)
		{
			window.postMessage(WindowMessageBag("StockDetailChanged", stockRowId), Grocy.BaseUrl);
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
