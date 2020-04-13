Grocy.BarCodeScannerTestingHitCount = 0;
Grocy.BarCodeScannerTestingMissCount = 0;

$("#scanned_barcode").on("blur", function (e)
{
	OnBarcodeScanned($("#scanned_barcode").val());
});

$("#scanned_barcode").keydown(function(event)
{
	if (event.keyCode === 13) //Enter
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
		$("#barcodescanner-start-button").removeAttr("disabled");
		$("#barcodescanner-start-button").removeClass("disabled");
	}
	else
	{
		$("#scanned_barcode").attr("disabled", "");
		$("#barcodescanner-start-button").attr("disabled", "");
		$("#barcodescanner-start-button").addClass("disabled");
	}
});

$("#expected_barcode").focus();
setTimeout(function()
{
	$("#barcodescanner-start-button").attr("disabled", "");
	$("#barcodescanner-start-button").addClass("disabled");
}, 200);

if (GetUriParam("barcode") !== undefined)
{
	$("#expected_barcode").val(GetUriParam("barcode"));
	setTimeout(function ()
	{
		$("#expected_barcode").keyup();
		$("#scanned_barcode").focus();
	}, 200);
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
		animateCSS("#miss-count", "pulse");
	}
	else
	{
		Grocy.BarCodeScannerTestingHitCount++;
		bgClass = "bg-success";

		$("#hit-count").text(Grocy.BarCodeScannerTestingHitCount);
		animateCSS("#hit-count", "pulse");
	}

	$("#scanned_codes").prepend("<option class='" + bgClass + "'>" + barcode + "</option>");
	setTimeout(function()
	{
		$("#scanned_barcode").val("");

		if (!$(":focus").is($("#expected_barcode")))
		{
			$("#scanned_barcode").focus();
		}
	}, 200);
}

$(document).on("Grocy.BarcodeScanned", function(e, barcode, target)
{
	if (target !== "#scanned_barcod")
	{
		return;
	}
	
	OnBarcodeScanned(barcode);
});
