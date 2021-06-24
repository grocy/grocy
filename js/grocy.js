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
import { animateCSS, BoolVal, EmptyElementWhenMatches } from "./helpers/extensions";
import Translator from "gettext-translator";
import { WindowMessageBag } from './helpers/messagebag';
import * as components from './components';

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
		this.QuantityUnitEditFormRedirectUri = config.QuantityUnitEditFormRedirectUri;
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
		this.Locale = config.Locale;
		this.fullcalendarEventSources = config.fullcalendarEventSources;
		this.internalRecipes = config.internalRecipes;
		this.recipesResolved = config.recipesResolved;

		this.Components = {};
		this.initComponents = [];

		this.RootGrocy = null;

		// Init some classes
		this.Api = new GrocyApi(this);

		// Merge dynamic and static locales
		var strings = this.Api.LoadLanguageSync(this.Locale);
		if (strings == null)
		{
			console.error("Could not load locale " + this.Locale + ", fallback to en.");
			strings = this.Api.LoadLanguageSync("en");
		}
		Object.assign(strings.messages[""], config.GettextPo.messages[""]);
		this.Translator = new Translator(strings);

		this.FrontendHelpers = new GrocyFrontendHelpers(this, this.Api);
		this.WakeLock = new WakeLock(this);
		this.UISound = new UISound(this);
		this.Nightmode = new Nightmode(this);

		this.HeaderClock = new HeaderClock(this);
		var self = this;

		// defer some stuff until DOM content has loaded
		document.addEventListener("DOMContentLoaded", function() 
		{
			self.Nightmode.Initialize();
			self.Nightmode.StartWatchdog();

			// DB Changed Handling
			if (self.UserId !== -1)
			{

				self.Api.Get('system/db-changed-time',
					function(result)
					{
						self.DatabaseChangedTime = moment(result.changed_time);
						setInterval(self.CheckDatabase(), 60000);
						// Increase the idle time once every second
						// On any interaction it will be reset to 0 (see above)
						setInterval(self.IncrementIdleTime(), 1000);
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		});

		// save the config
		this.config = config;

		if (!this.CalendarFirstDayOfWeek.isEmpty())
		{
			moment.updateLocale(moment.locale(), {
				week: {
					dow: this.CalendarFirstDayOfWeek
				}
			});
		}
	}

	static createSingleton(config, view)
	{
		if (window.Grocy === undefined)
		{
			var grocy = new GrocyClass(config);
			window.Grocy = grocy;
			// Check if the database has changed once a minute
			window.onmousemove = grocy.ResetIdleTime;
			window.onmousedown = grocy.ResetIdleTime;
			window.onclick = grocy.ResetIdleTime;
			window.onscroll = grocy.ResetIdleTime;
			window.onkeypress = grocy.ResetIdleTime;

			window.__t = function(key, ...placeholder) { return grocy.translate(key, ...placeholder) };
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
			window.EmptyElementWhenMatches = EmptyElementWhenMatches;
			window.animateCSS = animateCSS;

			// load the view
			grocy.LoadView(view);
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

	Use(componentName, scope = null)
	{
		let scopeName = scope || "";
		// initialize Components only once per scope
		if (this.initComponents.find(elem => elem == componentName + scopeName))
			return this.Components[componentName + scopeName];

		if (Object.prototype.hasOwnProperty.call(components, componentName))
		{
			// add-then-init to resolve circular dependencies
			this.initComponents.push(componentName);
			var component = new components[componentName](this, scope);
			this.Components[componentName + scopeName] = component;
			return component;
		}
		else
		{
			console.error("Unable to find component " + componentName);
		}
	}

	LoadView(viewName, scope = null)
	{
		if (Object.prototype.hasOwnProperty.call(window, viewName + "View"))
		{
			window[viewName + "View"](this, scope);
		}
		else
		{
			console.error("Could not load view " + viewName + ', not loaded yet.');
		}
	}

	PreloadView(viewName, loadCSS = false, cb = () => { })
	{
		if (!Object.prototype.hasOwnProperty.call(window, viewName + "View"))
		{
			$.ajax({
				dataType: "script",
				cache: true,
				url: this.FormatUrl('/viewjs/' + viewName + '.js'),
				success: cb
			});
			if (loadCSS)
			{
				$("<link/>", {
					rel: "stylesheet",
					type: "text/css",
					href: this.FormatUrl('/css/viewcss/' + viewName + '.cs')
				}).appendTo("head");
			}
		}
		else
		{
			cb();
		}
	}

	UndoStockBooking(bookingId)
	{
		var self = this;
		this.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
			function()
			{
				toastr.success(self.translate("Booking successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	UndoStockTransaction(transactionId)
	{
		var self = this;
		this.Api.Post('stock/transactions/' + transactionId.toString() + '/undo', {},
			function()
			{
				toastr.success(self.translate("Transaction successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	UndoChoreExecution(executionId)
	{
		var self = this;
		this.Api.Post('chores/executions/' + executionId.toString() + '/undo', {},
			function()
			{
				toastr.success(self.translate("Chore execution successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	UndoChargeCycle(chargeCycleId)
	{
		var self = this;
		this.Api.Post('batteries/charge-cycles/' + chargeCycleId.toString() + '/undo', {},
			function()
			{
				toastr.success(self.translate("Charge cycle successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	UndoStockBookingEntry(bookingId, stockRowId)
	{
		var self = this;
		this.Api.Post('stock/bookings/' + bookingId.toString() + '/undo', {},
			function()
			{
				window.postMessage(WindowMessageBag("StockEntryChanged", stockRowId), self.BaseUrl);
				toastr.success(self.translate("Booking successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	ScanModeSubmit(singleUnit = true)
	{
		if (BoolVal(this.UserSettings.scan_mode_purchase_enabled))
		{
			if (singleUnit)
			{
				$("#display_amount").val(1);
				$(".input-group-productamountpicker").trigger("change");
			}

			this.FrontendHelpers.ValidateForm("purchase-form");
			if (document.getElementById("purchase-form").checkValidity() === true)
			{
				$('#save-purchase-button').click();
			}
			else
			{
				toastr.warning(this.translate("Scan mode is on but not all required fields could be populated automatically"));
				this.UISound.Error();
			}
		}
	}

	GetUriParam(key)
	{
		var currentUri = window.location.search.substring(1);
		var vars = currentUri.split('&');

		for (var i = 0; i < vars.length; i++)
		{
			var currentParam = vars[i].split('=');

			if (currentParam[0] === key)
			{
				return currentParam[1] === undefined ? true : decodeURIComponent(currentParam[1]);
			}
		}
	}

	UpdateUriParam(key, value)
	{
		var queryParameters = new URLSearchParams(window.location.search);
		queryParameters.set(key, value);
		window.history.replaceState({}, "", decodeURIComponent(`${window.location.pathname}?${queryParameters}`));
	}

	RemoveUriParam(key)
	{
		var queryParameters = new URLSearchParams(window.location.search);
		queryParameters.delete(key);
		window.history.replaceState({}, "", decodeURIComponent(`${window.location.pathname}?${queryParameters}`));
	}
}

// also set on the Window object, just because.
window.GrocyClass = GrocyClass;

export default GrocyClass;