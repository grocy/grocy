function barcodescannertestingView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	Grocy.Use("barcodescanner");
	Grocy.BarCodeScannerTestingHitCount = 0;
	Grocy.BarCodeScannerTestingMissCount = 0;

	$scope("#scanned_barcode").on("blur", function(e)
	{
		OnBarcodeScanned($scope("#scanned_barcode").val());
	});

	$scope("#scanned_barcode").keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();
			OnBarcodeScanned($scope("#scanned_barcode").val());
		}
	});

	$scope("#expected_barcode").on("keyup", function(e)
	{
		if ($scope("#expected_barcode").val().length > 1)
		{
			$scope("#scanned_barcode").removeAttr("disabled");
			$scope("#barcodescanner-start-button").removeAttr("disabled");
			$scope("#barcodescanner-start-button").removeClass("disabled");
		}
		else
		{
			$scope("#scanned_barcode").attr("disabled", "");
			$scope("#barcodescanner-start-button").attr("disabled", "");
			$scope("#barcodescanner-start-button").addClass("disabled");
		}
	});

	$scope("#expected_barcode").focus();
	setTimeout(function()
	{
		$scope("#barcodescanner-start-button").attr("disabled", "");
		$scope("#barcodescanner-start-button").addClass("disabled");
	}, 200);

	if (Grocy.GetUriParam("barcode") !== undefined)
	{
		$scope("#expected_barcode").val(Grocy.GetUriParam("barcode"));
		setTimeout(function()
		{
			$scope("#expected_barcode").keyup();
			$scope("#scanned_barcode").focus();
		}, 200);
	}

	function OnBarcodeScanned(barcode)
	{
		if (barcode.length === 0)
		{
			return;
		}

		var bgClass = "";
		if (barcode != $scope("#expected_barcode").val())
		{
			Grocy.BarCodeScannerTestingMissCount++;
			bgClass = "bg-danger";

			$scope("#miss-count").text(Grocy.BarCodeScannerTestingMissCount);
			animateCSS("#miss-count", "pulse");
		}
		else
		{
			Grocy.BarCodeScannerTestingHitCount++;
			bgClass = "bg-success";

			$scope("#hit-count").text(Grocy.BarCodeScannerTestingHitCount);
			animateCSS("#hit-count", "pulse");
		}

		$scope("#scanned_codes").prepend("<option class='" + bgClass + "'>" + barcode + "</option>");
		setTimeout(function()
		{
			$("#scanned_barcode").val("");

			if (!$scope(":focus").is($scope("#expected_barcode")))
			{
				$scope("#scanned_barcode").focus();
			}
		}, 200);
	}

	$(document).on("Grocy.BarcodeScanned", function(e, barcode, target)
	{
		if (target !== "#scanned_barcode")
		{
			return;
		}

		OnBarcodeScanned(barcode);
	});

}


window.barcodescannertestingView = barcodescannertestingView
