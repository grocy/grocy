Grocy.Components.CameraBarcodeScanner = {};

Grocy.Components.CameraBarcodeScanner.Scanner = null;
Grocy.Components.CameraBarcodeScanner.LiveVideoSizeAdjusted = false;
Grocy.Components.CameraBarcodeScanner.CameraSelectLoaded = false;
Grocy.Components.CameraBarcodeScanner.TorchIsOn = false;

Grocy.Components.CameraBarcodeScanner.CheckCapabilities = async function()
{
	var track = Grocy.Components.CameraBarcodeScanner.Scanner.stream.getVideoTracks()[0];
	var capabilities = {};
	if (typeof track.getCapabilities === 'function')
	{
		capabilities = track.getCapabilities();
	}

	// Init camera select dropdown
	if (!Grocy.Components.CameraBarcodeScanner.CameraSelectLoaded)
	{
		var cameraSelect = document.querySelector('.cameraSelect');
		var cameras = await Grocy.Components.CameraBarcodeScanner.Scanner.listVideoInputDevices();
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

	// Reduce the height of the video if it's higher than the viewport
	if (!Grocy.Components.CameraBarcodeScanner.LiveVideoSizeAdjusted)
	{
		var bc = document.getElementById('camerabarcodescanner-container');
		if (bc)
		{
			var bcAspectRatio = bc.offsetWidth / bc.offsetHeight;
			var settings = track.getSettings();
			if (bcAspectRatio > settings.aspectRatio)
			{
				var v = document.querySelector('#camerabarcodescanner-livestream');
				if (v)
				{
					var newWidth = v.clientWidth / bcAspectRatio * settings.aspectRatio + 'px';
					v.style.width = newWidth;
				}
			}

			Grocy.Components.CameraBarcodeScanner.LiveVideoSizeAdjusted = true;
		}
	}
}

Grocy.Components.CameraBarcodeScanner.StartScanning = function()
{
	Grocy.Components.CameraBarcodeScanner.Scanner.decodeFromVideoDevice(
		window.localStorage.getItem('cameraId'),
		document.querySelector("#camerabarcodescanner-livestream"),
		(result, error) =>
		{
			if (error)
			{
				return;
			}

			Grocy.Components.CameraBarcodeScanner.StopScanning();

			$(document).trigger("Grocy.BarcodeScanned", [result.getText(), Grocy.Components.CameraBarcodeScanner.CurrentTarget]);
			$(".modal").last().modal("hide");
		}
	)
		.then(() =>
		{
			Grocy.Components.CameraBarcodeScanner.CheckCapabilities();

			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_AUTO_TORCH_ON_WITH_CAMERA)
			{
				setTimeout(function()
				{
					Grocy.Components.CameraBarcodeScanner.TorchToggle(Grocy.Components.CameraBarcodeScanner.Scanner.stream.getVideoTracks()[0]);
				}, 250);
			}
		})
		.catch((error) =>
		{
			Grocy.FrontendHelpers.ShowGenericError("Error while initializing the barcode scanning library", error.message);
			toastr.info(__t("Camera access is only possible when supported and allowed by your browser and when Grocy is served via a secure (https://) connection"));
			window.localStorage.removeItem("cameraId");
			setTimeout(function()
			{
				$(".modal").last().modal("hide");
			}, Grocy.FormFocusDelay);
			return;
		})
}

Grocy.Components.CameraBarcodeScanner.StopScanning = function()
{
	Grocy.Components.CameraBarcodeScanner.Scanner.reset();

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
		message: '<div id="camerabarcodescanner-container" class="col"><video id="camerabarcodescanner-livestream"></div></div>',
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
					if (Grocy.Components.CameraBarcodeScanner.Scanner.stream)
					{
						Grocy.Components.CameraBarcodeScanner.TorchToggle(Grocy.Components.CameraBarcodeScanner.Scanner.stream.getVideoTracks()[0]);
					}
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
	dialog.find('.bootbox-body').append('<div class="form-group pb-0 pt-2 my-1 d-block cameraSelect-wrapper"><select class="custom-control custom-select cameraSelect"></select></div>');
	var cameraSelect = document.querySelector('.cameraSelect');
	cameraSelect.onchange = function()
	{
		window.localStorage.setItem('cameraId', cameraSelect.value);
		Grocy.Components.CameraBarcodeScanner.Scanner.reset();
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

	Grocy.Components.CameraBarcodeScanner.Scanner = new ZXing.BrowserMultiFormatReader(new Map().set(ZXing.DecodeHintType.POSSIBLE_FORMATS, [
		ZXing.BarcodeFormat.EAN_8,
		ZXing.BarcodeFormat.EAN_13,
		ZXing.BarcodeFormat.CODE_39,
		ZXing.BarcodeFormat.CODE_128,
		ZXing.BarcodeFormat.DATA_MATRIX,
		ZXing.BarcodeFormat.QR_CODE,
	]));

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
