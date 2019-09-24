Grocy.Components.BarcodeScanner = { };

Grocy.Components.BarcodeScanner.StartScanning = function()
{
	Grocy.Components.BarcodeScanner.DecodedCodesCount = 0;
	Grocy.Components.BarcodeScanner.DecodedCodesErrorCount = 0;

	Quagga.init({
		inputStream: {
			name: "Live",
			type: "LiveStream",
			target: document.querySelector("#barcodescanner-livestream"),
			constraints: {
				width: 436,
				height: 327,
				facingMode: "environment"
			}
		},
		locator: {
			patchSize: "medium",
			halfSample: false,
			debug: {
				showCanvas: true,
				showPatches: true,
				showFoundPatches: true,
				showSkeleton: true,
				showLabels: true,
				showPatchLabels: true,
				showRemainingPatchLabels: true,
				boxFromPatches: {
					showTransformed: true,
					showTransformedBox: true,
					showBB: true
				}
			}
		},
		numOfWorkers: 2,
		frequency: 10,
		decoder: {
			readers: [
				"ean_reader",
				"ean_8_reader",
				"code_128_reader"
			],
			debug: {
				showCanvas: true,
				showPatches: true,
				showFoundPatches: true,
				showSkeleton: true,
				showLabels: true,
				showPatchLabels: true,
				showRemainingPatchLabels: true,
				boxFromPatches: {
					showTransformed: true,
					showTransformedBox: true,
					showBB: true
				}
			}
		},
		locate: true
	}, function(error)
	{
		if (error)
		{
			Grocy.FrontendHelpers.ShowGenericError("Error while initializing the barcode scanning library", error.message);
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 500);
			return;
		}
		Quagga.start();
	});
}

Grocy.Components.BarcodeScanner.StopScanning = function()
{
	Quagga.stop();
	
	Grocy.Components.BarcodeScanner.DecodedCodesCount = 0;
	Grocy.Components.BarcodeScanner.DecodedCodesErrorCount = 0;

	bootbox.hideAll();
}

Quagga.onDetected(function(result)
{
	$.each(result.codeResult.decodedCodes, function(id, error)
	{
		if (error.error != undefined)
		{
			Grocy.Components.BarcodeScanner.DecodedCodesCount++;
			Grocy.Components.BarcodeScanner.DecodedCodesErrorCount += parseFloat(error.error);
		}
	});

	if (Grocy.Components.BarcodeScanner.DecodedCodesErrorCount / Grocy.Components.BarcodeScanner.DecodedCodesCount < 0.15)
	{
		Grocy.Components.BarcodeScanner.StopScanning();
		$(document).trigger("Grocy.BarcodeScanned", [result.codeResult.code]);
	}
});

Quagga.onProcessed(function(result)
{
	var drawingCtx = Quagga.canvas.ctx.overlay;
	var drawingCanvas = Quagga.canvas.dom.overlay;

	if (result)
	{
		if (result.boxes)
		{
			drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
			result.boxes.filter(function(box)
			{
				return box !== result.box;
			}).forEach(function(box)
			{
				Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "yellow", lineWidth: 4 });
			});
		}

		if (result.box)
		{
			Quagga.ImageDebug.drawPath(result.box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 4 });
		}

		if (result.codeResult && result.codeResult.code)
		{
			Quagga.ImageDebug.drawPath(result.line, { x: 'x', y: 'y' }, drawingCtx, { color: "red", lineWidth: 4 });
		}
	}
});

$(document).on("click", "#barcodescanner-start-button", function(e)
{
	e.preventDefault();

	bootbox.dialog({
		message: '<div id="barcodescanner-container" class="col"><div id="barcodescanner-livestream"></div></div>',
		title: __t('Scan a barcode'),
		onEscape: function()
		{
			Grocy.Components.BarcodeScanner.StopScanning();
		},
		size: 'big',
		backdrop: true,
		closeButton: false,
		buttons: {
			cancel: {
				label: __t('Cancel'),
				className: 'btn-secondary responsive-button',
				callback: function()
				{
					Grocy.Components.BarcodeScanner.StopScanning();
				}
			}
		}
	});

	Grocy.Components.BarcodeScanner.StartScanning();
});


setTimeout(function()
{
	$(".barcodescanner-input:visible").after('<a id="barcodescanner-start-button" class="btn btn-sm btn-primary text-white"><i class="fas fa-camera"></i></a>');
}, 50);
