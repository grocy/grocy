import { BoolVal } from '../helpers/extensions';

// this class has side-effects and only works as a singleton. GrocyClass is responsible for handling that.
class WakeLock
{
	constructor(Grocy)
	{
		this.NoSleepJsIntance = null;
		this.InitDone = false;
		this.grocy = Grocy;

		var self = this; // jquery probably overrides this

		$("#keep_screen_on").on("change", function()
		{
			var value = $(this).is(":checked");
			if (value)
			{
				self.Enable();
			}
			else
			{
				self.Disable();
			}
		});

		this.observer = // Handle "Keep screen on while displaying a fullscreen-card" when the body class "fullscreen-card" has changed
			new MutationObserver(function(mutations)
			{
				if (BoolVal(Grocy.UserSettings.keep_screen_on_when_fullscreen_card) && !BoolVal(Grocy.UserSettings.keep_screen_on))
				{
					mutations.forEach(function(mutation)
					{
						if (mutation.attributeName === "class")
						{
							var attributeValue = $(mutation.target).prop(mutation.attributeName);
							if (attributeValue.contains("fullscreen-card"))
							{
								self.Enable();
							}
							else
							{
								self.Disable();
							}
						}
					});
				}
			});
		this.observer.observe(document.body, {
			attributes: true
		});

		// Enabling NoSleep.Js only works in a user input event handler,
		// so if the user wants to keep the screen on always,
		// do this in on the first click on anything

		$(document).click(function()
		{
			if (Grocy.WakeLock.InitDone === false && BoolVal(Grocy.UserSettings.keep_screen_on))
			{
				self.Enable();
			}

			self.InitDone = true;
		});
	}

	Enable()
	{
		if (this.NoSleepJsIntance === null)
		{
			this.NoSleepJsIntance = new NoSleep();
		}
		this.NoSleepJsIntance.enable();
		this.InitDone = true;
	}

	Disable()
	{
		if (this.NoSleepJsIntance !== null)
		{
			this.NoSleepJsIntance.disable();
		}
	}
}

export { WakeLock }