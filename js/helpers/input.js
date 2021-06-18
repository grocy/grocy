/* global DocumentTouch */

// TODO: DocumentTouch is deprecated
//       https://developer.mozilla.org/en-US/docs/Web/API/DocumentTouch
function IsTouchInputDevice()
{
	if (("ontouchstart" in window) || window.DocumentTouch && document instanceof DocumentTouch)
	{
		return true;
	}

	return false;
}


export { IsTouchInputDevice }