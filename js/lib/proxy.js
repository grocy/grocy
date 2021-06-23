
import { GrocyFrontendHelpers } from '../helpers/frontend';
import * as components from '../components';

class GrocyProxy
{

	constructor(RootGrocy, scopeSelector, config, url)
	{
		this.RootGrocy = RootGrocy;

		// proxy-local members, because they might not be set globally.
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

		// components are always local
		this.Components = {};
		this.initComponents = [];

		// scoping
		this.scopeSelector = scopeSelector;
		this.scope = $(scope);
		this.$scope = this.scope.find;
		var queryString = url.split('?', 2);
		this.virtualUrl = queryString.length == 2 ? queryString[1] : ""; // maximum two parts

		this.config = config;

		this.configProxy = Proxy.revocable(this.config, {
			get: function(target, prop, receiver)
			{
				if (Object.prototype.hasOwnProperty.call(target, prop))
				{
					return Reflect.get(...arguments);
				}
				else
				{
					return Reflect.get(rootGrocy.config, prop, target);
				}
			}
		})

		// This is where the magic happens!
		// basically, this Proxy object checks if a member is defined in this proxy class, 
		// and returns it if so.
		// If not, the prop is handed over to the root grocy instance.
		this.grocyProxy = Proxy.revocable(this, {
			get: function(target, prop, receiver)
			{
				if (Object.prototype.hasOwnProperty.call(target, prop))
				{
					return Reflect.get(...arguments)
				}
				else
				{
					return Reflect.get(RootGrocy, prop, receiver);
				}
			}
		});

		// scoped variants of some helpers
		this.FrontendHelpers = new GrocyFrontendHelpers(this, RootGrocy.Api, this.scopeSelector);
	}

	Unload()
	{
		this.grocyProxy.revoke();
		this.configProxy.revoke();
	}

	Use(componentName, scope = null)
	{
		let scopeName = scope || "";
		// initialize Components only once per scope
		if (this.initComponents.find(elem => elem == componentName + scopeName))
			return this.components[componentName + scopeName];

		if (Object.prototype.hasOwnProperty.call(components, componentName))
		{
			// add-then-init to resolve circular dependencies
			this.initComponents.push(componentName);
			var component = components[componentName](this, scope);
			this.components[componentName + scopeName] = component;
			return component;
		}
		else
		{
			console.error("Unable to find component " + componentName);
		}
	}

	LoadView(viewName)
	{
		if (Object.prototype.hasOwnProperty.call(window, viewName + "View"))
		{
			window[viewName + "View"](this, this.scopeSelector);
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
		params = {}
		var vars = this.virtualUrl.split("&");

		for (part of vars)
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
		params = {}
		var vars = this.virtualUrl.split("&");

		for (part of vars)
		{
			var lkey, lvalue;
			[lkey, lvalue = null] = part.split('=');

			if (lkey == key)
				continue;

			params[lkey] = lvalue;
		}

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
}

export { GrocyProxy }