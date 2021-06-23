import Quagga from '@ericblade/quagga2/dist/quagga';

class barcodescanner
{
	constructor(Grocy, scopeSelector = null)
	{
		this.Grocy = Grocy;

		this.scopeSelector = scopeSelector;
		this.scope = scopeSelector != null ? $(scopeSelector) : $(document);
		this.$ = scopeSelector != null ? $(scopeSelector).find : $;

		// init component
		this.LiveVideoSizeAdjusted = false;

		var self = this;

		Quagga.onDetected(function(result)
		{
			$.each(result.codeResult.decodedCodes, function(id, error)
			{
				if (error.error != undefined)
				{
					self.DecodedCodesCount++;
					self.DecodedCodesErrorCount += parseFloat(error.error);
				}
			});

			if (self.DecodedCodesErrorCount / self.DecodedCodesCount < 0.15)
			{
				self.StopScanning();
				$(document).trigger("Grocy.BarcodeScanned", [result.codeResult.code, self.CurrentTarget]);
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

		this.scope.on("click", "#barcodescanner-start-button", async function(e)
		{
			e.preventDefault();
			var inputElement = $(e.currentTarget).prev();
			if (inputElement.hasAttr("disabled"))
			{
				// Do nothing and disable the barcode scanner start button
				$(e.currentTarget).addClass("disabled");
				return;
			}

			self.CurrentTarget = inputElement.attr("data-target");

			var dialog = bootbox.dialog({
				message: '<div id="barcodescanner-container" class="col"><div id="barcodescanner-livestream"></div><div id="debug"></div></div>',
				title: self.Grocy.translate('Scan a barcode'),
				onEscape: function()
				{
					self.StopScanning();
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
							self.TorchOn(Quagga.CameraAccess.getActiveTrack());
							return false;
						}
					},
					cancel: {
						label: self.Grocy.translate('Cancel'),
						className: 'btn-secondary responsive-button',
						callback: function()
						{
							self.StopScanning();
						}
					}
				}
			});

			// Add camera select to existing dialog
			dialog.find('.bootbox-body').append('<div class="form-group py-0 my-1 cameraSelect-wrapper"><select class="custom-control custom-select cameraSelect"><select class="custom-control custom-select cameraSelect" style="display: none"></select></div>');
			var cameraSelect = document.querySelector('.cameraSelect');

			if (cameraSelect != null)
			{
				var cameras = await Quagga.CameraAccess.enumerateVideoDevices();
				cameras.forEach(camera =>
				{
					var option = document.createElement("option");
					option.text = camera.label ? camera.label : camera.deviceId; // Use camera label if it exists, else show device id
					option.value = camera.deviceId;
					cameraSelect.appendChild(option);
				});

				// Set initial value to preferred camera if one exists - and if not, start out empty
				cameraSelect.value = window.localStorage.getItem('cameraId');

				cameraSelect.onchange = function()
				{
					window.localStorage.setItem('cameraId', cameraSelect.value);
					Quagga.stop();
					self.StartScanning();
				};
			}

			self.StartScanning();
		});

		setTimeout(function()
		{
			this.$scope(".barcodescanner-input:visible").each(function()
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
	}

	async CheckCapabilities()
	{
		var track = Quagga.CameraAccess.getActiveTrack();
		var capabilities = {};
		if (typeof track.getCapabilities === 'function')
		{
			capabilities = track.getCapabilities();
		}

		// If there is more than 1 camera, show the camera selection
		var cameras = await Quagga.CameraAccess.enumerateVideoDevices();
		var cameraSelect = this.$('.cameraSelect-wrapper');
		if (cameraSelect.length)
		{
			cameraSelect[0].style.display = cameras.length > 1 ? 'inline-block' : 'none';
		}

		// Check if the camera is capable to turn on a torch.
		var canTorch = typeof capabilities.torch === 'boolean' && capabilities.torch
		// Remove the torch button, if either the device can not torch or AutoTorchOn is set.
		var node = this.$('.torch');
		if (node.length)
		{
			node[0].style.display = canTorch && !this.Grocy.FeatureFlags.GROCY_FEATURE_FLAG_AUTO_TORCH_ON_WITH_CAMERA ? 'inline-block' : 'none';
		}
		// If AutoTorchOn is set, turn on the torch.
		if (canTorch && this.Grocy.FeatureFlags.GROCY_FEATURE_FLAG_AUTO_TORCH_ON_WITH_CAMERA)
		{
			this.TorchOn(track);
		}

		// Reduce the height of the video, if it's higher than then the viewport
		if (!this.LiveVideoSizeAdjusted)
		{
			var bc = this.$('barcodescanner-container');
			if (bc.length)
			{
				bc = bc[0]
				var bcAspectRatio = bc.offsetWidth / bc.offsetHeight;
				var settings = track.getSettings();
				if (bcAspectRatio > settings.aspectRatio)
				{
					var v = this.$('#barcodescanner-livestream video')
					if (v.length)
					{
						v = v[0];
						var c = this.$('#barcodescanner-livestream canvas')[0]
						var newWidth = v.clientWidth / bcAspectRatio * settings.aspectRatio + 'px';
						v.style.width = newWidth;
						c.style.width = newWidth;
					}
				}

				this.LiveVideoSizeAdjusted = true;
			}
		}
	}

	StartScanning()
	{
		this.DecodedCodesCount = 0;
		this.DecodedCodesErrorCount = 0;
		var self = this;

		Quagga.init({
			inputStream: {
				name: "Live",
				type: "LiveStream",
				target: this.$("#barcodescanner-livestream")[0],
				constraints: {
					facingMode: "environment",
					...(window.localStorage.getItem('cameraId') && { deviceId: window.localStorage.getItem('cameraId') }) // If preferred cameraId is set, request to use that specific camera
				}
			},
			locator: {
				patchSize: self.Grocy.UserSettings.quagga2_patchsize,
				halfSample: self.Grocy.UserSettings.quagga2_halfsample,
				debug: {
					showCanvas: self.Grocy.UserSettings.quagga2_debug,
					showPatches: self.Grocy.UserSettings.quagga2_debug,
					showFoundPatches: self.Grocy.UserSettings.quagga2_debug,
					showSkeleton: self.Grocy.UserSettings.quagga2_debug,
					showLabels: self.Grocy.UserSettings.quagga2_debug,
					showPatchLabels: self.Grocy.UserSettings.quagga2_debug,
					showRemainingPatchLabels: self.Grocy.UserSettings.quagga2_debug,
					boxFromPatches: {
						showTransformed: self.Grocy.UserSettings.quagga2_debug,
						showTransformedBox: self.Grocy.UserSettings.quagga2_debug,
						showBB: self.Grocy.UserSettings.quagga2_debug
					}
				}
			},
			numOfWorkers: self.Grocy.UserSettings.quagga2_numofworkers,
			frequency: self.Grocy.UserSettings.quagga2_frequency,
			decoder: {
				readers: [
					"ean_reader",
					"ean_8_reader",
					"code_128_reader"
				],
				debug: {
					showCanvas: self.Grocy.UserSettings.quagga2_debug,
					showPatches: self.Grocy.UserSettings.quagga2_debug,
					showFoundPatches: self.Grocy.UserSettings.quagga2_debug,
					showSkeleton: self.Grocy.UserSettings.quagga2_debug,
					showLabels: self.Grocy.UserSettings.quagga2_debug,
					showPatchLabels: self.Grocy.UserSettings.quagga2_debug,
					showRemainingPatchLabels: self.Grocy.UserSettings.quagga2_debug,
					boxFromPatches: {
						showTransformed: self.Grocy.UserSettings.quagga2_debug,
						showTransformedBox: self.Grocy.UserSettings.quagga2_debug,
						showBB: self.Grocy.UserSettings.quagga2_debug
					}
				}
			},
			locate: true
		}, function(error)
		{
			// error *needs* to be logged here, otherwise the stack trace is lying.
			console.error(error);
			if (error)
			{
				self.Grocy.FrontendHelpers.ShowGenericError("Error while initializing the barcode scanning library", error.message);
				toastr.info(self.Grocy.translate("Camera access is only possible when supported and allowed by your browser and when grocy is served via a secure (https://) connection"));
				window.localStorage.removeItem("cameraId");
				setTimeout(function()
				{
					bootbox.hideAll();
				}, 500);
				return;
			}

			this.CheckCapabilities();

			Quagga.start();
		});
	}

	StopScanning()
	{
		Quagga.stop();

		this.DecodedCodesCount = 0;
		this.DecodedCodesErrorCount = 0;

		bootbox.hideAll();
	}

	TorchOn(track)
	{
		if (track)
		{
			track.applyConstraints({
				advanced: [
					{
						torch: true
					}
				]
			});
		}
	}
}

export { barcodescanner }