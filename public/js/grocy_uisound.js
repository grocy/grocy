Grocy.UISound = {};

Grocy.UISound.Play = function(url)
{
	new Audio(url).play();
}

Grocy.UISound.AskForPermission = function()
{
	Grocy.UISound.Play(U("/uisounds/silence.mp3"));
}

Grocy.UISound.Success = function()
{
	Grocy.UISound.Play(U("/uisounds/success.mp3"));
}

Grocy.UISound.Error = function()
{
	Grocy.UISound.Play(U("/uisounds/error.mp3"));
}

Grocy.UISound.BarcodeScannerBeep = function()
{
	Grocy.UISound.Play(U("/uisounds/barcodescannerbeep.mp3"));
}
