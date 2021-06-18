// bwipjs is broken, needs to be explicitly imported.
import bwipJs from '../../node_modules/bwip-js/dist/bwip-js.mjs';

function QrCodeImgHtml(text)
{
	var dummyCanvas = document.createElement("canvas");
	var img = document.createElement("img");

	bwipJs.toCanvas(dummyCanvas, {
		bcid: "qrcode",
		text: text,
		scale: 4,
		includetext: false
	});
	img.src = dummyCanvas.toDataURL("image/png");
	img.classList.add("qr-code");

	return img.outerHTML;
}

export { QrCodeImgHtml }