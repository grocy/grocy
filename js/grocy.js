import { GrocyApi } from './lib/api';
import { RefreshContextualTimeago } from "./configs/timeago";
import { LoadImagesLazy } from "./configs/lazy";
import { setDatatableDefaults } from "./configs/datatable";
import { GrocyFrontendHelpers } from "./helpers/frontend";
import { setInitialGlobalState } from "./configs/globalstate";
import { RefreshLocaleNumberDisplay, RefreshLocaleNumberInput } from "./helpers/numberdisplay";
import { WakeLock } from "./lib/WakeLock";
import { UISound } from "./lib/UISound";
import { Nightmode } from "./lib/nightmode";
import { HeaderClock } from "./helpers/clock";
import { animateCSS, BoolVal, Delay, EmptyElementWhenMatches, GetUriParam, RemoveUriParam, UpdateUriParam } from "./helpers/extensions";
import Translator from "gettext-translator";

import "./helpers/string";

class GrocyClass
{
	constructor(config)
	{
		// set up properties from config
		this.UserSettings = config.UserSettings;
		this.Mode = config.Mode;
		this.UserId = config.UserId;
		this.ActiveNav = config.ActiveNav;
		this.CalendarFirstDayOfWeek = config.CalendarFirstDayOfWeek;
		this.DatabaseChangedTime = null;
		this.IdleTime = 0;
		this.BaseUrl = config.BaseUrl;
		this.Currency = config.Currency;
		this.FeatureFlags = config.FeatureFlags;
		this.QuantityUnits = config.QuantityUnits;
		this.QuantityUnitConversionsResolved = config.QuantityUnitConversionsResolved || [];
		this.MealPlanFirstDayOfWeek = config.MealPlanFirstDayOfWeek;
		this.EditMode = config.EditMode;
		this.EditObjectId = config.EditObjectId;
		this.DefaultMinAmount = config.DefaultMinAmount;
		this.UserPictureFileName = config.UserPictureFileName;
		this.EditObjectParentId = config.EditObjectParentId;
		this.EditObjectParentName = config.EditObjectParentName;
		this.EditObject = config.EditObject;
		this.EditObjectProduct = config.EditObjectProduct;
		this.RecipePictureFileName = config.RecipePictureFileName;
		this.InstructionManualFileNameName = config.InstructionManualFileNameName;

		this.Components = {};

		// Init some classes
		this.Api = new GrocyApi(this);
		this.Translator = new Translator(config.GettextPo);
		this.FrontendHelpers = new GrocyFrontendHelpers(this.Api);
		this.WakeLock = new WakeLock(this);
		this.UISound = new UISound(this);
		this.Nightmode = new Nightmode(this);
		this.Nightmode.StartWatchdog();
		this.HeaderClock = new HeaderClock(this);

		// save the config
		this.config = config;

		if (!this.CalendarFirstDayOfWeek.isEmpty())
		{
			moment.updateLocale(moment.locale(), {
				week: {
					dow: Grocy.CalendarFirstDayOfWeek
				}
			});
		}

		// DB Changed Handling
		if (this.UserId !== -1)
		{
			var self = this;
			this.Api.Get('system/db-changed-time',
				function(result)
				{
					self.DatabaseChangedTime = moment(result.changed_time);
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	}

	static createSingleton(config)
	{
		if (window.Grocy === undefined)
		{
			var grocy = new GrocyClass(config);
			window.Grocy = grocy;
			// Check if the database has changed once a minute

			setInterval(grocy.CheckDatabase(), 60000);
			// Increase the idle time once every second
			// On any interaction it will be reset to 0 (see above)
			setInterval(grocy.IncrementIdleTime(), 1000);
			window.onmousemove = grocy.ResetIdleTime;
			window.onmousedown = grocy.ResetIdleTime;
			window.onclick = grocy.ResetIdleTime;
			window.onscroll = grocy.ResetIdleTime;
			window.onkeypress = grocy.ResetIdleTime;

			window.__t = function(key, ...placeholder) { return grocy.translate(key, ...placeholder) };;
			window.__n = function(key, ...placeholder) { return grocy.translaten(key, ...placeholder) };
			window.U = path => grocy.FormatUrl(path);

			setInitialGlobalState(grocy);

			RefreshContextualTimeago();
			window.RefreshContextualTimeago = RefreshContextualTimeago;
			RefreshLocaleNumberDisplay();
			window.RefreshLocaleNumberDisplay = RefreshLocaleNumberDisplay;
			RefreshLocaleNumberInput();
			window.RefreshLocaleNumberInput = RefreshLocaleNumberInput;
			LoadImagesLazy();
			window.LoadImagesLazy = LoadImagesLazy;
			setDatatableDefaults(grocy);

			// add some more functions to the global space
			window.Delay = Delay;
			window.GetUriParam = GetUriParam;
			window.UpdateUriParam = UpdateUriParam;
			window.RemoveUriParam = RemoveUriParam;
			window.EmptyElementWhenMatches = EmptyElementWhenMatches;
			window.animateCSS = animateCSS;
		}
		return window.Grocy;
	}

	translate(text, ...placeholderValues)
	{
		if (this.Mode === "dev")
		{
			var text2 = text;
			this.Api.Post('system/log-missing-localization', { "text": text2 });
		}

		return this.Translator.__(text, ...placeholderValues)
	}
	translaten(number, singularForm, pluralForm)
	{
		if (this.Mode === "dev")
		{
			var singularForm2 = singularForm;
			this.Api.Post('system/log-missing-localization', { "text": singularForm2 });
		}

		return this.Translator.n__(singularForm, pluralForm, number, number)
	}
	FormatUrl(relativePath)
	{
		return this.BaseUrl.replace(/\/$/, '') + relativePath;
	}

	// If a change is detected, reload the current page, but only if already idling for at least 50 seconds,
	// when there is no unsaved form data and when the user enabled auto reloading
	CheckDatabase()
	{
		var self = this;
		this.Api.Get('system/db-changed-time',
			function(result)
			{
				var newDbChangedTime = moment(result.changed_time);
				if (newDbChangedTime.isAfter(self.DatabaseChangedTime))
				{
					if (self.IdleTime >= 50)
					{
						if (BoolVal(self.UserSettings.auto_reload_on_db_change) && $("form.is-dirty").length === 0 && !$("body").hasClass("fullscreen-card"))
						{
							window.location.reload();
						}
					}

					self.DatabaseChangedTime = newDbChangedTime;
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	ResetIdleTime()
	{
		this.IdleTime = 0;
	}

	IncrementIdleTime()
	{
		this.IdleTime += 1;
	}

	UndoStockBooking(bookingId)
	{
		this.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
			function(result)
			{
				toastr.success(__t("Booking successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	UndoStockTransaction(transactionId)
	{
		this.Api.Post('stock/transactions/' + transactionId.toString() + '/undo', {},
			function(result)
			{
				toastr.success(__t("Transaction successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	UndoChoreExecution(executionId)
	{
		this.Api.Post('chores/executions/' + executionId.toString() + '/undo', {},
			function(result)
			{
				toastr.success(__t("Chore execution successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	UndoChargeCycle(chargeCycleId)
	{
		this.Api.Post('batteries/charge-cycles/' + chargeCycleId.toString() + '/undo', {},
			function(result)
			{
				toastr.success(__t("Charge cycle successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	UndoStockBookingEntry(bookingId, stockRowId)
	{
		Grocy.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
			function(result)
			{
				window.postMessage(WindowMessageBag("StockEntryChanged", stockRowId), Grocy.BaseUrl);
				toastr.success(__t("Booking successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	};
}

// also set on the Window object, just because.
window.GrocyClass = GrocyClass;

export default GrocyClass;