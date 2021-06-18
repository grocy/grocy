function IsTouchInputDevice()
{
	if (("ontouchstart" in window) || window.DocumentTouch && document instanceof DocumentTouch)
	{
		return true;
	}

	return false;
}


export { IsTouchInputDevice }