var stockDetailTable = $('#stock-detail-table').DataTable({
	'order': [[2, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	],
});
$('#stock-detail-table tbody').removeClass("d-none");

function bootBoxModal(message) {
	bootbox.dialog({
		message: message,
		size: 'large',
		backdrop: true,
		closeButton: false,
		buttons: {
			cancel: {
				label: __t('Cancel'),
				className: 'btn-secondary responsive-button',
				callback: function()
				{
					bootbox.hideAll();
				}
			}
		}
	});
}



$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var productId = Grocy.Components.ProductPicker.GetValue();
 
        if ( ( isNaN( productId ) ||
		productId == "" ||
		//assume productId is in the first column
		productId == data[1] ) )
        {
            return true;
        }
        return false;
    }
);

$(document).ready(function() {
	Grocy.Components.ProductPicker.GetPicker().trigger('change');
} );

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

	var wasSpoiled = $(e.currentTarget).hasClass("product-consume-button-spoiled");

	Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': wasSpoiled, 'location_id': locationId, 'stock_entry_id': specificStockEntryId},
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					var toastMessage = __t('Removed %1$s of %2$s from stock', consumeAmount.toString() + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBooking(' + bookingResponse.id + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';
					if (wasSpoiled)
					{
						toastMessage += " (" + __t("Spoiled") + ")";
					}

					Grocy.FrontendHelpers.EndUiBusy();
					toastr.success(toastMessage);
					RefreshStockDetailRow(stockRowId);
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
					toastr.success(__t('Marked %1$s of %2$s as opened', 1 + " " + productQuName, productName) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBooking(' + bookingResponse.id + ')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>');
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

$(document).on("click", ".stock-name-cell", function(e)
{
	Grocy.Components.ProductCard.Refresh($(e.currentTarget).attr("data-stock-id"));
	$("#stockdetail-productcard-modal").modal("show");
});

$(document).on("click", ".product-purchase-button", function(e)
{
	e.preventDefault();

	var productId = $(e.currentTarget).attr("data-product-id");

	bootBoxModal('<iframe height="650px" class="embed-responsive" src="' + U("/purchase?embedded&product=") + productId.toString() + '"></iframe>');
});

$(document).on("click", ".product-transfer-button", function(e)
{

	e.preventDefault();

	var productId = $(e.currentTarget).attr("data-product-id");
	var locationId = $(e.currentTarget).attr('data-location-id');
	var specificStockEntryId = $(e.currentTarget).attr('data-stock-id');
	bootBoxModal('<iframe height="650px" class="embed-responsive" src="' + U("/transfer?embedded&product=") + productId.toString() + '&locationId=' + locationId.toString() + '&stockId=' + specificStockEntryId.toString() + '"></iframe>');

});

$(document).on("click", ".product-consume-custom-amount-button", function(e)
{
	e.preventDefault();

	var productId = $(e.currentTarget).attr("data-product-id");
	var locationId = $(e.currentTarget).attr('data-location-id');
	var specificStockEntryId = $(e.currentTarget).attr('data-stock-id');

	bootBoxModal('<iframe height="650px" class="embed-responsive" src="' + U("/consume?embedded&product=") + productId.toString() + '&locationId=' + locationId.toString() + '&stockId=' + specificStockEntryId.toString() + '"></iframe>');

});

$(document).on("click", ".product-inventory-button", function(e)
{
	e.preventDefault();

	var productId = $(e.currentTarget).attr("data-product-id");

	bootBoxModal('<iframe height="650px" class="embed-responsive" src="' + U("/inventory?embedded&product=") + productId.toString() + '"></iframe>');
});

$(document).on("click", ".product-stockedit-button", function(e)
{
	e.preventDefault();

	var productId = $(e.currentTarget).attr("data-product-id");
	var stockRowId = $(e.currentTarget).attr("data-id");

	bootBoxModal('<iframe height="650px" class="embed-responsive" src="' + U("/stockedit?embedded&product=") + productId.toString() + '&stockRowId=' + stockRowId.toString() + '"></iframe>');
});

$(document).on("click", ".product-add-to-shopping-list-button", function(e)
{
	e.preventDefault();

	var productId = $(e.currentTarget).attr("data-product-id");

	bootBoxModal('<iframe height="650px" class="embed-responsive" src="' + U("/shoppinglistitem/new?embedded&updateexistingproduct&product=") + productId.toString() + '"></iframe>');
});

function RefreshStockDetailRow(stockRowId)
{
	Grocy.Api.Get("stock/" + stockRowId + "/entry",
		function(result)
		{
			var stockRow = $('#stock-' + stockRowId + '-row');
			var now = moment();

			stockRow.removeClass("table-warning");
			stockRow.removeClass("table-danger");
			stockRow.removeClass("table-info");
			stockRow.removeClass("d-none");
			stockRow.removeAttr("style");

			if (result == null || result.amount == 0)
			{
				stockRow.fadeOut(500, function()
				{
					//$(this).tooltip("hide");
					$(this).addClass("d-none");
				});
			}
			else
			{
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

				var locationName = "";
				Grocy.Api.Get("objects/locations/" + result.location_id,
					function(locationResult)
					{
						locationName = locationResult.name;
					},
					function(xhr)
					{
						console.error(xhr);
					});
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
			}

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
