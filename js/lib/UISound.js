class UISound
{
	constructor(Grocy)
	{
		this.grocy = Grocy;
		this.U = path => this.grocy.FormatUrl(path);
	}

	Play(url)
	{
		new Audio(url).play();
	}

	AskForPermission()
	{
		this.Play(this.U("/uisounds/silence.mp3"));
	}

	Success()
	{
		this.Play(this.U("/uisounds/success.mp3"));
	}

	Error()
	{
		this.Play(this.U("/uisounds/error.mp3"));
	}

	BarcodeScannerBeep()
	{
		this.Play(this.U("/uisounds/barcodescannerbeep.mp3"));
	}
}

export { UISound }