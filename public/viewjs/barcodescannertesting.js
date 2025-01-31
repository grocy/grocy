Grocy.BarCodeScannerTestingHitCount = 0;
Grocy.BarCodeScannerTestingMissCount = 0;

$("#scanned_barcode").on("blur", function(e)
{
	OnBarcodeScanned($("#scanned_barcode").val());
});

$("#scanned_barcode").keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();
		OnBarcodeScanned($("#scanned_barcode").val());
	}
});

$("#expected_barcode").on("keyup", function(e)
{
	if ($("#expected_barcode").val().length > 1)
	{
		$("#scanned_barcode").removeAttr("disabled");
		$("#camerabarcodescanner-start-button").removeAttr("disabled");
		$("#camerabarcodescanner-start-button").removeClass("disabled");
	}
	else
	{
		$("#scanned_barcode").attr("disabled", "");
		$("#camerabarcodescanner-start-button").attr("disabled", "");
		$("#camerabarcodescanner-start-button").addClass("disabled");
	}
});

setTimeout(function()
{
	$("#camerabarcodescanner-start-button").attr("disabled", "");
	$("#camerabarcodescanner-start-button").addClass("disabled");
	$("#expected_barcode").focus();
}, Grocy.FormFocusDelay);

if (GetUriParam("barcode") !== undefined)
{
	$("#expected_barcode").val(GetUriParam("barcode"));
	setTimeout(function()
	{
		$("#expected_barcode").keyup();
		$("#scanned_barcode").focus();
	}, Grocy.FormFocusDelay);
}

function OnBarcodeScanned(barcode)
{
	if (barcode.length === 0)
	{
		return;
	}

	var bgClass = "";
	if (barcode != $("#expected_barcode").val())
	{
		Grocy.BarCodeScannerTestingMissCount++;
		bgClass = "bg-danger";

		$("#miss-count").text(Grocy.BarCodeScannerTestingMissCount);
		animateCSS("#miss-count", "flash");
	}
	else
	{
		Grocy.BarCodeScannerTestingHitCount++;
		bgClass = "bg-success";

		$("#hit-count").text(Grocy.BarCodeScannerTestingHitCount);
		animateCSS("#hit-count", "flash");
	}

	$("#scanned_codes").prepend("<option class='" + bgClass + "'>" + barcode + "</option>");
	setTimeout(function()
	{
		$("#scanned_barcode").val("");

		if (!$(":focus").is($("#expected_barcode")))
		{
			$("#scanned_barcode").focus();
		}
	}, Grocy.FormFocusDelay);
}

$(document).on("Grocy.BarcodeScanned", function(e, barcode, target)
{
	if (target !== "#scanned_barcode")
	{
		return;
	}

	OnBarcodeScanned(barcode);
});
