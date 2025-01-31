Grocy.Components.CameraBarcodeScanner = {};

Grocy.Components.CameraBarcodeScanner.LiveVideoSizeAdjusted = false;
Grocy.Components.CameraBarcodeScanner.CameraSelectLoaded = false;
Grocy.Components.CameraBarcodeScanner.TorchIsOn = false;

Grocy.Components.CameraBarcodeScanner.CheckCapabilities = async function()
{
	var track = Quagga.CameraAccess.getActiveTrack();
	var capabilities = {};
	if (typeof track.getCapabilities === 'function')
	{
		capabilities = track.getCapabilities();
	}

	// Init camera select dropdown
	if (!Grocy.Components.CameraBarcodeScanner.CameraSelectLoaded)
	{
		var cameraSelect = document.querySelector('.cameraSelect');
		var cameras = await Quagga.CameraAccess.enumerateVideoDevices();
		cameras.forEach(camera =>
		{
			var option = document.createElement("option");
			option.text = camera.label;
			option.value = camera.deviceId;
			if (track.label == camera.label)
			{
				option.selected = "selected";
			}
			cameraSelect.appendChild(option);
		});

		Grocy.Components.CameraBarcodeScanner.CameraSelectLoaded = true;
	}

	// Check if the camera is capable to turn on a torch
	var hasTorch = typeof capabilities.torch === 'boolean' && capabilities.torch;

	// Remove the torch button if the select camera doesn't have a torch
	if (!hasTorch)
	{
		document.querySelector('.camerabarcodescanner-modal .modal-footer').setAttribute('style', 'display:none !important;');
	}
	else
	{
		document.querySelector('.camerabarcodescanner-modal .modal-footer').setAttribute('style', 'flex;');
	}

	// Reduce the height of the video, if it's higher than then the viewport
	if (!Grocy.Components.CameraBarcodeScanner.LiveVideoSizeAdjusted)
	{
		var bc = document.getElementById('camerabarcodescanner-container');
		if (bc)
		{
			var bcAspectRatio = bc.offsetWidth / bc.offsetHeight;
			var settings = track.getSettings();
			if (bcAspectRatio > settings.aspectRatio)
			{
				var v = document.querySelector('#camerabarcodescanner-livestream video');
				if (v)
				{
					var c = document.querySelector('#camerabarcodescanner-livestream canvas')
					var newWidth = v.clientWidth / bcAspectRatio * settings.aspectRatio + 'px';
					v.style.width = newWidth;
					c.style.width = newWidth;
				}
			}

			Grocy.Components.CameraBarcodeScanner.LiveVideoSizeAdjusted = true;
		}
	}
}

Grocy.Components.CameraBarcodeScanner.StartScanning = function()
{
	Grocy.Components.CameraBarcodeScanner.DecodedCodesCount = 0;
	Grocy.Components.CameraBarcodeScanner.DecodedCodesErrorCount = 0;

	Quagga.init({
		inputStream: {
			name: "Live",
			type: "LiveStream",
			target: document.querySelector("#camerabarcodescanner-livestream"),
			constraints: {
				facingMode: "environment",
				...(window.localStorage.getItem('cameraId') && { deviceId: window.localStorage.getItem('cameraId') }), // If preferred cameraId is set, request to use that specific camera
			}
		},
		locator: {
			patchSize: Grocy.UserSettings.quagga2_patchsize,
			halfSample: Grocy.UserSettings.quagga2_halfsample,
			debug: {
				showCanvas: Grocy.UserSettings.quagga2_debug,
				showPatches: Grocy.UserSettings.quagga2_debug,
				showFoundPatches: Grocy.UserSettings.quagga2_debug,
				showSkeleton: Grocy.UserSettings.quagga2_debug,
				showLabels: Grocy.UserSettings.quagga2_debug,
				showPatchLabels: Grocy.UserSettings.quagga2_debug,
				showRemainingPatchLabels: Grocy.UserSettings.quagga2_debug,
				boxFromPatches: {
					showTransformed: Grocy.UserSettings.quagga2_debug,
					showTransformedBox: Grocy.UserSettings.quagga2_debug,
					showBB: Grocy.UserSettings.quagga2_debug
				}
			}
		},
		numOfWorkers: Grocy.UserSettings.quagga2_numofworkers,
		frequency: Grocy.UserSettings.quagga2_frequency,
		decoder: {
			readers: [
				"ean_reader",
				"ean_8_reader",
				"code_128_reader",
				"code_39_reader"
			],
			debug: {
				showCanvas: Grocy.UserSettings.quagga2_debug,
				showPatches: Grocy.UserSettings.quagga2_debug,
				showFoundPatches: Grocy.UserSettings.quagga2_debug,
				showSkeleton: Grocy.UserSettings.quagga2_debug,
				showLabels: Grocy.UserSettings.quagga2_debug,
				showPatchLabels: Grocy.UserSettings.quagga2_debug,
				showRemainingPatchLabels: Grocy.UserSettings.quagga2_debug,
				boxFromPatches: {
					showTransformed: Grocy.UserSettings.quagga2_debug,
					showTransformedBox: Grocy.UserSettings.quagga2_debug,
					showBB: Grocy.UserSettings.quagga2_debug
				}
			}
		},
		locate: true
	}, function(error)
	{
		if (error)
		{
			Grocy.FrontendHelpers.ShowGenericError("Error while initializing the barcode scanning library", error.message);
			toastr.info(__t("Camera access is only possible when supported and allowed by your browser and when Grocy is served via a secure (https://) connection"));
			window.localStorage.removeItem("cameraId");
			setTimeout(function()
			{
				$(".modal").last().modal("hide");
			}, Grocy.FormFocusDelay);
			return;
		}

		Grocy.Components.CameraBarcodeScanner.CheckCapabilities();
		Quagga.start();

		if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_AUTO_TORCH_ON_WITH_CAMERA)
		{
			setTimeout(function()
			{
				Grocy.Components.CameraBarcodeScanner.TorchToggle(Quagga.CameraAccess.getActiveTrack());
			}, 250);
		}
	});
}

Grocy.Components.CameraBarcodeScanner.StopScanning = function()
{
	Quagga.stop();

	Grocy.Components.CameraBarcodeScanner.DecodedCodesCount = 0;
	Grocy.Components.CameraBarcodeScanner.DecodedCodesErrorCount = 0;
	Grocy.Components.CameraBarcodeScanner.LiveVideoSizeAdjusted = false;
	Grocy.Components.CameraBarcodeScanner.CameraSelectLoaded = false;
	Grocy.Components.CameraBarcodeScanner.TorchIsOn = false;
}

Grocy.Components.CameraBarcodeScanner.TorchToggle = function(track)
{
	if (track)
	{
		Grocy.Components.CameraBarcodeScanner.TorchIsOn = !Grocy.Components.CameraBarcodeScanner.TorchIsOn;
		track.applyConstraints({
			advanced: [
				{
					torch: Grocy.Components.CameraBarcodeScanner.TorchIsOn
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
			Grocy.Components.CameraBarcodeScanner.DecodedCodesCount++;
			Grocy.Components.CameraBarcodeScanner.DecodedCodesErrorCount += Number.parseFloat(error.error);
		}
	});

	if ((Grocy.Components.CameraBarcodeScanner.DecodedCodesErrorCount / Grocy.Components.CameraBarcodeScanner.DecodedCodesCount < 0.15) ||
		(Grocy.Components.CameraBarcodeScanner.DecodedCodesErrorCount == 0 && Grocy.Components.CameraBarcodeScanner.DecodedCodesCount == 0 && result.codeResult.code.length != 0))
	{
		Grocy.Components.CameraBarcodeScanner.StopScanning();
		$(document).trigger("Grocy.BarcodeScanned", [result.codeResult.code, Grocy.Components.CameraBarcodeScanner.CurrentTarget]);
		$(".modal").last().modal("hide");
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
			drawingCtx.clearRect(0, 0, Number.parseInt(drawingCanvas.getAttribute("width")), Number.parseInt(drawingCanvas.getAttribute("height")));
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

$(document).on("click", "#camerabarcodescanner-start-button", async function(e)
{
	e.preventDefault();
	var inputElement = $(e.currentTarget).prev();
	if (inputElement.hasAttr("disabled"))
	{
		// Do nothing and disable the barcode scanner start button
		$(e.currentTarget).addClass("disabled");
		return;
	}

	Grocy.Components.CameraBarcodeScanner.CurrentTarget = inputElement.attr("data-target");

	var dialog = bootbox.dialog({
		message: '<div id="camerabarcodescanner-container" class="col"><div id="camerabarcodescanner-livestream"></div></div>',
		title: __t('Scan a barcode'),
		size: 'large',
		backdrop: true,
		closeButton: true,
		className: "form camerabarcodescanner-modal",
		buttons: {
			torch: {
				label: '<i class="fa-solid fa-lightbulb"></i>',
				className: 'btn-warning responsive-button torch',
				callback: function()
				{
					Grocy.Components.CameraBarcodeScanner.TorchToggle(Quagga.CameraAccess.getActiveTrack());
					return false;
				}
			}
		},
		onHide: function(e)
		{
			Grocy.Components.CameraBarcodeScanner.StopScanning();
		}
	});

	// Add camera select to existing dialog
	dialog.find('.bootbox-body').append('<div class="form-group py-0 my-1 d-block cameraSelect-wrapper"><select class="custom-control custom-select cameraSelect"></select></div>');
	var cameraSelect = document.querySelector('.cameraSelect');
	cameraSelect.onchange = function()
	{
		window.localStorage.setItem('cameraId', cameraSelect.value);
		Quagga.stop();
		Grocy.Components.CameraBarcodeScanner.StartScanning();
	};

	Grocy.Components.CameraBarcodeScanner.StartScanning();
});

Grocy.Components.CameraBarcodeScanner.InitDone = false;
Grocy.Components.CameraBarcodeScanner.Init = function()
{
	if (Grocy.Components.CameraBarcodeScanner.InitDone)
	{
		return;
	}

	$(".barcodescanner-input:visible").each(function()
	{
		if ($(this).hasAttr("disabled"))
		{
			$(this).after('<a id="camerabarcodescanner-start-button" class="btn btn-sm btn-primary text-white disabled" data-target="' + $(this).attr("data-target") + '"><i class="fa-solid fa-camera"></i></a>');
		}
		else
		{
			$(this).after('<a id="camerabarcodescanner-start-button" class="btn btn-sm btn-primary text-white" data-target="' + $(this).attr("data-target") + '"><i class="fa-solid fa-camera"></i></a>');
		}

		Grocy.Components.CameraBarcodeScanner.InitDone = true;
	});
}

setTimeout(function()
{
	Grocy.Components.CameraBarcodeScanner.Init();
}, 50);
