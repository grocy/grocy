Grocy.Components.BarcodeScanner = { };

Grocy.Components.BarcodeScanner.LiveVideoSizeAdjusted = false;
Grocy.Components.BarcodeScanner.CheckCapabilities = async function()
{
	var track = Quagga.CameraAccess.getActiveTrack();
	var capabilities = {};
	if (typeof track.getCapabilities === 'function') {
		capabilities = track.getCapabilities();
	}
	
	// If there is more than 1 camera, show the camera selection
	var cameras = await Quagga.CameraAccess.enumerateVideoDevices();
	var cameraSelect = document.querySelector('.cameraSelect-wrapper');
	if (cameraSelect) {
		cameraSelect.style.display = cameras.length > 1 ? 'inline-block' : 'none';
	}
	
	// Check if the camera is capable to turn on a torch.
	var canTorch = typeof capabilities.torch === 'boolean' && capabilities.torch
	// Remove the torch button, if either the device can not torch or AutoTorchOn is set.
	var node = document.querySelector('.torch');
	if (node) {
		node.style.display = canTorch && !Grocy.FeatureFlags.GROCY_FEATURE_FLAG_AUTO_TORCH_ON_WITH_CAMERA ? 'inline-block' : 'none';
	}
	// If AutoTorchOn is set, turn on the torch.
	if (canTorch && Grocy.FeatureFlags.GROCY_FEATURE_FLAG_AUTO_TORCH_ON_WITH_CAMERA) {
		Grocy.Components.BarcodeScanner.TorchOn(track);
	}

	// Reduce the height of the video, if it's heigher than then the viewport
	if (!Grocy.Components.BarcodeScanner.LiveVideoSizeAdjusted)
	{
		var bc = document.getElementById('barcodescanner-container');
		if (bc)
		{
			var bcAspectRatio = bc.offsetWidth / bc.offsetHeight;
			var settings = track.getSettings();
			if (bcAspectRatio > settings.aspectRatio)
			{
				var v = document.querySelector('#barcodescanner-livestream video')
				if (v)
				{
					var c = document.querySelector('#barcodescanner-livestream canvas')
					var newWidth = v.clientWidth / bcAspectRatio * settings.aspectRatio + 'px';
					v.style.width = newWidth;
					c.style.width = newWidth;
				}
			}

			Grocy.Components.BarcodeScanner.LiveVideoSizeAdjusted = true;
		}
	}
}

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
				facingMode: "environment",
				...(window.localStorage.getItem('cameraId') && {deviceId : window.localStorage.getItem('cameraId')}) // If preferred cameraId is set, request to use that specific camera
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
		numOfWorkers: Grocy.UserSettings.quagga2_numofworkers,
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
			toastr.info(__t("Camera access is only possible when supported and allowed by your browser and when grocy is served via a secure (https://) connection"));
			window.localStorage.removeItem("cameraId");
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 500);
			return;
		}

		Grocy.Components.BarcodeScanner.CheckCapabilities();

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

Grocy.Components.BarcodeScanner.TorchOn = function(track)
{
	if (track) {
		track.applyConstraints({ 
			advanced: [
				{ 
					torch: true
				}
			]
		});
	}
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
		$(document).trigger("Grocy.BarcodeScanned", [result.codeResult.code, Grocy.Components.BarcodeScanner.CurrentTarget]);
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

$(document).on("click", "#barcodescanner-start-button", async function(e)
{
	e.preventDefault();
	var inputElement = $(e.currentTarget).prev();
	if (inputElement.hasAttr("disabled"))
	{
		// Do nothing and disable the barcode scanner start button
		$(e.currentTarget).addClass("disabled");
		return;
	}

	Grocy.Components.BarcodeScanner.CurrentTarget = inputElement.attr("data-target");
	
	var dialog = bootbox.dialog({
		message: '<div id="barcodescanner-container" class="col"><div id="barcodescanner-livestream"></div></div>',
		title: __t('Scan a barcode'),
		onEscape: function()
		{
			Grocy.Components.BarcodeScanner.StopScanning();
		},
		size: 'big',
		backdrop: true,
		closeButton: true,
		buttons: {
			torch: {
				label: '<i class="far fa-lightbulb"></i>',
				className: 'btn-warning responsive-button torch',
				callback: function()
				{
					Grocy.Components.BarcodeScanner.TorchOn(Quagga.CameraAccess.getActiveTrack());
					return false;
           		}	
			},			
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
	
	// Add camera select to existing dialog
	dialog.find('.bootbox-body').append('<div class="form-group py-0 my-1 cameraSelect-wrapper"><select class="form-control cameraSelect"><select class="form-control cameraSelect" style="display: none"></select></div>');
	var cameraSelect = document.querySelector('.cameraSelect');
	
	var cameras = await Quagga.CameraAccess.enumerateVideoDevices();
	cameras.forEach(camera => {
		var option = document.createElement("option");
		option.text = camera.label ? camera.label : camera.deviceId; // Use camera label if it exists, else show device id
		option.value = camera.deviceId;
		cameraSelect.appendChild(option);
	});

	// Set initial value to preferred camera if one exists - and if not, start out empty
	cameraSelect.value = window.localStorage.getItem('cameraId');

	cameraSelect.onchange = function(){
		window.localStorage.setItem('cameraId', cameraSelect.value);
		Quagga.stop();
		Grocy.Components.BarcodeScanner.StartScanning();
	};
    
	Grocy.Components.BarcodeScanner.StartScanning();
});

setTimeout(function()
{
	$(".barcodescanner-input:visible").each(function()
	{
		if ($(this).hasAttr("disabled"))
		{
			$(this).after('<a id="barcodescanner-start-button" class="btn btn-sm btn-primary text-white disabled" data-target="' + $(this).attr("data-target") + '"><i class="fas fa-camera"></i></a>');
		}
		else
		{
			$(this).after('<a id="barcodescanner-start-button" class="btn btn-sm btn-primary text-white" data-target="' + $(this).attr("data-target") + '"><i class="fas fa-camera"></i></a>');
		}
	});
}, 50);
