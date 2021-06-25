
import { GrocyFrontendHelpers } from '../helpers/frontend';
import * as components from '../components';

class GrocyProxy
{
	constructor(RootGrocy, scopeSelector, config, url)
	{
		this.RootGrocy = RootGrocy;

		// proxy-local members, because they might not be set globally.
		this.QuantityUnits = config.QuantityUnits;
		this.QuantityUnitConversionsResolved = config.QuantityUnitConversionsResolved;
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

		// components are always local
		this.Components = {};
		this.initComponents = [];

		// scoping
		this.scopeSelector = scopeSelector;
		this.scope = null;
		this.$scope = null;

		var queryString = url.split('?', 2);
		this.virtualUrl = queryString.length == 2 ? queryString[1] : ""; // maximum two parts

		this.config = config;
		if (Object.prototype.hasOwnProperty.call(this.config, "UserSettings"))
		{
			// if we need to override UserSettings, we need to copy the object.
			// not great, but eh.
			Object.assign(this.config.UserSettings, RootGrocy.UserSettings);
			this.UserSettings = config.UserSettings;
		}

		this.unloaders = [];
	}

	Initialize(proxy)
	{
		this.proxy = proxy;
		this.scope = $(this.scopeSelector);
		var jScope = this.scope;
		this.$scope = (selector) => jScope.find(selector);
		this.FrontendHelpers = new GrocyFrontendHelpers(proxy, this.RootGrocy.Api, this.scopeSelector);
	}

	RegisterUnload(cb)
	{
		this.unloaders.push(cb);
	}

	Unload()
	{
		for (let component in this.Components)
		{
			var comp = this.Components[component];
			if (Object.prototype.hasOwnProperty.call(comp, "Unload"))
			{
				comp.Unload();
			}
		}

		let unloader = this.unloaders.pop();
		while (unloader !== undefined)
		{
			unloader();
			unloader = this.unloaders.pop();
		}
	}

	Use(componentName, scope = null)
	{
		let scopeName = scope || this.scopeSelector;
		// initialize Components only once per scope
		if (this.initComponents.find(elem => elem == componentName + scopeName))
			return this.Components[componentName + scopeName];

		if (Object.prototype.hasOwnProperty.call(components, componentName))
		{
			// add-then-init to resolve circular dependencies
			this.initComponents.push(componentName + scopeName);
			var component = new components[componentName](this.proxy, scopeName);
			this.Components[componentName + scopeName] = component;
			return component;
		}
		else
		{
			console.error("Unable to find component " + componentName);
		}
	}

	// URI params on integrated components don't work because they
	// don't have an URL. So let's fake it.
	GetUriParam(key)
	{
		var currentUri = this.virtualUrl;
		var vars = currentUri.split('&');

		for (var i = 0; i < vars.length; i++)
		{
			var currentParam = vars[i].split('=');

			if (currentParam[0] === key)
			{
				return currentParam[1] === undefined ? true : decodeURIComponent(currentParam[1]);
			}
		}
		return undefined;
	}

	UpdateUriParam(key, value)
	{
		var params = {}
		var vars = this.virtualUrl.split("&");

		for (let part of vars)
		{
			var lkey, lvalue;
			[lkey, lvalue = null] = part.split('=');

			params[lkey] = lvalue;
		}

		params[key] = value;

		var vurl = ""

		for ([key, value] of params)
		{
			vurl += "&" + key
			if (value != null)
			{
				vurl += '=' + encodeURIComponent(value);
			}
		}
		this.virtualUrl = vurl.substring(1); // remove leading &
	}

	RemoveUriParam(key)
	{
		var params = {}
		var vars = this.virtualUrl.split("&");

		for (let part of vars)
		{
			var lkey, lvalue;
			[lkey, lvalue = null] = part.split('=');

			if (lkey == key)
				continue;

			params[lkey] = lvalue;
		}

		var vurl = ""
		let value;
		for ([key, value] of params)
		{
			vurl += "&" + key
			if (value != null)
			{
				vurl += '=' + encodeURIComponent(value);
			}
		}
		this.virtualUrl = vurl.substring(1); // remove leading &
	}
}

export { GrocyProxy }