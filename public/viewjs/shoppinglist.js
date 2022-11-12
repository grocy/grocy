var shoppingListTable = $('#shoppinglist-table').DataTable({
	'order': [[1, 'asc']],
	"orderFixed": [[3, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 3 },
		{ 'visible': false, 'targets': 5 },
		{ 'visible': false, 'targets': 6 },
		{ 'visible': false, 'targets': 7 },
		{ 'visible': false, 'targets': 8 },
		{ "type": "custom-sort", "targets": 2 },
		{ "type": "html-num-fmt", "targets": 5 },
		{ "type": "html-num-fmt", "targets": 6 }
	].concat($.fn.dataTable.defaults.columnDefs),
	'rowGroup': {
		enable: true,
		dataSrc: 3
	}
});
$('#shoppinglist-table tbody').removeClass("d-none");
shoppingListTable.columns.adjust().draw();

var shoppingListPrintShadowTable = $('#shopping-list-print-shadow-table').DataTable({
	'order': [[1, 'asc']],
	"orderFixed": [[2, 'asc']],
	'columnDefs': [
		{ 'visible': false, 'targets': 2 },
		{ 'orderable': false, 'targets': '_all' }
	].concat($.fn.dataTable.defaults.columnDefs),
	'rowGroup': {
		enable: true,
		dataSrc: 2
	}
});
shoppingListPrintShadowTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	shoppingListTable.search(value).draw();
}, 200));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#status-filter").val("all");
	$("#search").trigger("keyup");
	$("#status-filter").trigger("change");
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

	shoppingListTable.column(shoppingListTable.colReorder.transpose(4)).search(value).draw();
});

$("#selected-shopping-list").on("change", function()
{
	var value = $(this).val();
	window.location.href = U('/shoppinglist?list=' + value);
});

$(".status-filter-message").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
});

$("#delete-selected-shopping-list").on("click", function()
{
	var objectName = $("#selected-shopping-list option:selected").text();
	var objectId = $("#selected-shopping-list").val();

	bootbox.confirm({
		message: __t('Are you sure to delete shopping list "%s"?', objectName),
		closeButton: false,
		buttons: {
			confirm: {
				label: __t('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: __t('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/shopping_lists/' + objectId, {},
					function(result)
					{
						window.location.href = U('/shoppinglist');
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		}
	});
});

$(document).on('click', '.shoppinglist-delete-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	var shoppingListItemId = $(e.currentTarget).attr('data-shoppinglist-id');
	Grocy.FrontendHelpers.BeginUiBusy();

	Grocy.Api.Delete('objects/shopping_list/' + shoppingListItemId, {},
		function(result)
		{
			animateCSS("#shoppinglistitem-" + shoppingListItemId + "-row", "fadeOut", function()
			{
				Grocy.FrontendHelpers.EndUiBusy();
				$("#shoppinglistitem-" + shoppingListItemId + "-row").remove();
				OnListItemRemoved();
			});
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
	if ($(e.currentTarget).attr("data-product-id") != "")
	{
		Grocy.Components.ProductCard.Refresh($(e.currentTarget).attr("data-product-id"));
		$("#shoppinglist-productcard-modal").modal("show");
	}
});

$(document).on('click', '#add-products-below-min-stock-amount', function(e)
{
	Grocy.Api.Post('stock/shoppinglist/add-missing-products', { "list_id": $("#selected-shopping-list").val() },
		function(result)
		{
			window.location.href = U('/shoppinglist?list=' + $("#selected-shopping-list").val());
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '#add-overdue-expired-products', function(e)
{
	Grocy.Api.Post('stock/shoppinglist/add-overdue-products', { "list_id": $("#selected-shopping-list").val() },
		function(result)
		{
			Grocy.Api.Post('stock/shoppinglist/add-expired-products', { "list_id": $("#selected-shopping-list").val() },
				function(result)
				{
					window.location.href = U('/shoppinglist?list=' + $("#selected-shopping-list").val());
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '#clear-shopping-list', function(e)
{
	var confirmMessage = __t('Are you sure to empty shopping list "%s"?', $("#selected-shopping-list option:selected").text());
	if (!BoolVal(Grocy.FeatureFlags.GROCY_FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS))
	{
		confirmMessage = __t('Are you sure to empty the shopping list?');
	}

	bootbox.confirm({
		message: confirmMessage,
		closeButton: false,
		buttons: {
			confirm: {
				label: __t('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: __t('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.FrontendHelpers.BeginUiBusy();

				Grocy.Api.Post('stock/shoppinglist/clear', { "list_id": $("#selected-shopping-list").val() },
					function(result)
					{
						window.location.reload();
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy();
						console.error(xhr);
					}
				);
			}
		}
	});
});

$(document).on('click', '.shopping-list-stock-add-workflow-list-item-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	var href = $(e.currentTarget).attr('href');

	$("#shopping-list-stock-add-workflow-purchase-form-frame").attr("src", href);
	$("#shopping-list-stock-add-workflow-modal").modal("show");

	if (Grocy.ShoppingListToStockWorkflowAll)
	{
		$("#shopping-list-stock-add-workflow-purchase-item-count").removeClass("d-none");
		$("#shopping-list-stock-add-workflow-purchase-item-count").text(__t("Adding shopping list item %1$s of %2$s", Grocy.ShoppingListToStockWorkflowCurrent, Grocy.ShoppingListToStockWorkflowCount));
		$("#shopping-list-stock-add-workflow-skip-button").removeClass("d-none");
	}
	else
	{
		$("#shopping-list-stock-add-workflow-skip-button").addClass("d-none");
	}
});

Grocy.ShoppingListToStockWorkflowAll = false;
Grocy.ShoppingListToStockWorkflowCount = 0;
Grocy.ShoppingListToStockWorkflowCurrent = 0;
Grocy.ShoppingListAddToStockButtonList = [];
$(document).on('click', '#add-all-items-to-stock-button', function(e)
{
	Grocy.ShoppingListToStockWorkflowAll = true;
	Grocy.ShoppingListAddToStockButtonList = $(".shopping-list-stock-add-workflow-list-item-button");
	Grocy.ShoppingListToStockWorkflowCount = Grocy.ShoppingListAddToStockButtonList.length;
	Grocy.ShoppingListToStockWorkflowCurrent++;
	$(".shopping-list-stock-add-workflow-list-item-button").first().click();
});

$("#shopping-list-stock-add-workflow-modal").on("hidden.bs.modal", function(e)
{
	Grocy.ShoppingListToStockWorkflowAll = false;
	Grocy.ShoppingListToStockWorkflowCount = 0;
	Grocy.ShoppingListToStockWorkflowCurrent = 0;
	Grocy.ShoppingListAddToStockButtonList = [];
})

$(window).on("message", function(e)
{
	var data = e.originalEvent.data;

	if (data.Message === "AfterItemAdded")
	{
		$(".shoppinglist-delete-button[data-shoppinglist-id='" + data.Payload + "']").click();
	}
	else if (data.Message === "Ready")
	{
		if (!Grocy.ShoppingListToStockWorkflowAll)
		{
			$("#shopping-list-stock-add-workflow-modal").modal("hide");
		}
		else
		{
			Grocy.ShoppingListToStockWorkflowCurrent++;
			if (Grocy.ShoppingListToStockWorkflowCurrent <= Grocy.ShoppingListToStockWorkflowCount)
			{
				Grocy.ShoppingListAddToStockButtonList[Grocy.ShoppingListToStockWorkflowCurrent - 1].click();
			}
			else
			{
				$("#shopping-list-stock-add-workflow-modal").modal("hide");
			}
		}
	}
});

$(document).on('click', '#shopping-list-stock-add-workflow-skip-button', function(e)
{
	e.preventDefault();

	window.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
});

$(document).on('click', '.order-listitem-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var listItemId = $(e.currentTarget).attr('data-item-id');

	var done = 1;
	if ($(e.currentTarget).attr('data-item-done') == 1)
	{
		done = 0;
	}

	$(e.currentTarget).attr('data-item-done', done);

	Grocy.Api.Put('objects/shopping_list/' + listItemId, { 'done': done },
		function()
		{
			if (done == 1)
			{
				$('#shoppinglistitem-' + listItemId + '-row').addClass("text-muted");
				$('#shoppinglistitem-' + listItemId + '-row').addClass("text-strike-through");
			}
			else
			{
				$('#shoppinglistitem-' + listItemId + '-row').removeClass("text-muted");
				$('#shoppinglistitem-' + listItemId + '-row').removeClass("text-strike-through");
			}

			Grocy.FrontendHelpers.EndUiBusy();
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
			console.error(xhr);
		}
	);


	var statusInfoCell = $("#shoppinglistitem-" + listItemId + "-status-info");
	if (done == 1)
	{
		statusInfoCell.text(statusInfoCell.text().replace("xxUNDONExx", "xxDONExx"));
	}
	else
	{
		statusInfoCell.text(statusInfoCell.text().replace("xxDONExx", "xxUNDONExx"));
	}
	shoppingListTable.rows().invalidate().draw(false);

	$("#status-filter").trigger("change");
});

function OnListItemRemoved()
{
	if ($(".shopping-list-stock-add-workflow-list-item-button").length === 0)
	{
		$("#add-all-items-to-stock-button").addClass("disabled");
	}
}
OnListItemRemoved();

$(document).on("click", "#print-shopping-list-button", function(e)
{
	var dialogHtml = ' \
	<div class="text-center"><h5>' + __t('Print options') + '</h5><hr></div> \
	<div class="custom-control custom-checkbox"> \
		<input id="print-show-header" \
			 checked \
			class="form-check-input custom-control-input" \
			type="checkbox" \
			value="1"> \
		<label class="form-check-label custom-control-label" \
			for="print-show-header">' + __t('Show header') + ' \
		</label> \
	</div> \
	<div class="custom-control custom-checkbox"> \
		<input id="print-group-by-product-group" \
			 checked \
			class="form-check-input custom-control-input" \
			type="checkbox" \
			value="1"> \
		<label class="form-check-label custom-control-label" \
			for="print-group-by-product-group">' + __t('Group by product group') + ' \
		</label> \
	</div> \
	<h5 class="pt-3 pb-0">' + __t('Layout type') + '</h5> \
	<div class="custom-control custom-radio"> \
		<input id="print-layout-type-table" \
			checked \
			class="custom-control-input" \
			type="radio" \
			name="print-layout-type" \
			value="print-layout-type-table"> \
		<label class="custom-control-label" \
			for="print-layout-type-table">' + __t('Table') + ' \
		</label> \
	</div> \
	<div class="custom-control custom-radio"> \
		<input id="print-layout-type-list" \
			class="custom-control-input" \
			type="radio" \
			name="print-layout-type" \
			value="print-layout-type-list"> \
		<label class="custom-control-label" \
			for="print-layout-type-list">' + __t('List') + ' \
		</label> \
	</div>';

	var sizePrintDialog = 'medium';
	var printButtons = {
		cancel: {
			label: __t('Cancel'),
			className: 'btn-secondary',
			callback: function()
			{
				bootbox.hideAll();
			}
		},
		printtp: {
			label: __t('Thermal printer'),
			className: 'btn-secondary',
			callback: function()
			{
				bootbox.hideAll();
				var printHeader = $("#print-show-header").prop("checked");
				var thermalPrintDialog = bootbox.dialog({
					title: __t('Printing'),
					message: '<p><i class="fa fa-spin fa-spinner"></i> ' + __t('Connecting to printer...') + '</p>'
				});
				//Delaying for one second so that the alert can be closed
				setTimeout(function()
				{
					Grocy.Api.Get('print/shoppinglist/thermal?list=' + $("#selected-shopping-list").val() + '&printHeader=' + printHeader,
						function(result)
						{
							bootbox.hideAll();
						},
						function(xhr)
						{
							console.error(xhr);
							var validResponse = true;
							try
							{
								var jsonError = JSON.parse(xhr.responseText);
							} catch (e)
							{
								validResponse = false;
							}
							if (validResponse)
							{
								thermalPrintDialog.find('.bootbox-body').html(__t('Unable to print') + '<br><pre><code>' + jsonError.error_message + '</pre></code>');
							} else
							{
								thermalPrintDialog.find('.bootbox-body').html(__t('Unable to print') + '<br><pre><code>' + xhr.responseText + '</pre></code>');
							}
						}
					);
				}, 1000);
			}
		},
		ok: {
			label: __t('Print'),
			className: 'btn-primary responsive-button',
			callback: function()
			{
				bootbox.hideAll();
				$('.modal-backdrop').remove();
				$(".print-timestamp").text(moment().format("l LT"));

				$("#description-for-print").html($("#description").val());
				if ($("#description").text().isEmpty())
				{
					$("#description-for-print").parent().addClass("d-print-none");
				}

				if (!$("#print-show-header").prop("checked"))
				{
					$("#print-header").addClass("d-none");
				}

				if (!$("#print-group-by-product-group").prop("checked"))
				{
					shoppingListPrintShadowTable.rowGroup().enable(false);
					shoppingListPrintShadowTable.order.fixed({});
					shoppingListPrintShadowTable.draw();
				}

				$(".print-layout-container").addClass("d-none");
				$("." + $("input[name='print-layout-type']:checked").val()).removeClass("d-none");

				window.print();
			}
		}
	}

	if (!Grocy.FeatureFlags["GROCY_FEATURE_FLAG_THERMAL_PRINTER"])
	{
		delete printButtons['printtp'];
		sizePrintDialog = 'small';
	}

	bootbox.dialog({
		message: dialogHtml,
		size: sizePrintDialog,
		backdrop: true,
		closeButton: false,
		className: "d-print-none",
		buttons: printButtons
	});
});

$(document).on("change", "input[name='print-layout-type']", function(e)
{
	if (this.value == "print-layout-type-list")
	{
		$("#print-group-by-product-group").prop("checked", false);
		$("#print-group-by-product-group").attr("disabled", "");
	}
	else
	{
		$("#print-group-by-product-group").removeAttr("disabled");
	}
});

$("#description").on("summernote.change", function()
{
	$("#save-description-button").removeClass("disabled");

	if ($("#description").summernote("isEmpty"))
	{
		$("#clear-description-button").addClass("disabled");
	}
	else
	{
		$("#clear-description-button").removeClass("disabled");
	}
});

$(document).on("click", "#save-description-button", function(e)
{
	e.preventDefault();
	document.activeElement.blur();

	Grocy.Api.Put('objects/shopping_lists/' + $("#selected-shopping-list").val(), { description: $("#description").val() },
		function(result)
		{
			$("#save-description-button").addClass("disabled");
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on("click", "#clear-description-button", function(e)
{
	e.preventDefault();
	document.activeElement.blur();

	$("#description").summernote("reset");
	$("#save-description-button").click();
});

$("#description").trigger("summernote.change");
$("#save-description-button").addClass("disabled");

$(window).on("message", function(e)
{
	var data = e.originalEvent.data;

	if (data.Message === "ShoppingListChanged")
	{
		window.location.href = U('/shoppinglist?list=' + data.Payload);
	}
});

var dummyCanvas = document.createElement("canvas");
$("img.barcode").each(function()
{
	var img = $(this);
	var barcode = img.attr("data-barcode").replace(/\D/g, "");

	var barcodeType = "code128";
	if (barcode.length == 8)
	{
		barcodeType = "ean8";
	}
	else if (barcode.length == 13)
	{
		barcodeType = "ean13";
	}

	bwipjs.toCanvas(dummyCanvas, {
		bcid: barcodeType,
		text: barcode,
		height: 5,
		includetext: false
	});

	img.attr("src", dummyCanvas.toDataURL("image/png"));
});

if ($(window).width() < 768 || !Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK)
{
	$("#filter-container").removeClass("border-bottom");
}
