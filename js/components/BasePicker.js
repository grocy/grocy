class BasePicker
{

	constructor(Grocy, basename, scopeSelector = null)
	{
		this.Grocy = Grocy;

		this.scopeSelector = scopeSelector;
		this.scope = scopeSelector != null ? $(scopeSelector) : $(document);
		var jScope = this.scope;
		this.$ = scopeSelector != null ? (selector) => jScope.find(selector) : $;

		this.picker = null;
		this.input_element = null;

		this.basename = basename;
		this.hasCombobox = false;
	}

	prefill()
	{
		if (this.picker == null)
		{
			console.error("Cannot prefill " + this.basename + ", picker not set.");
			return;
		}

		var doFokus = false;
		var prefillByName = this.picker.parent().data('prefill-by-name').toString();
		if (typeof prefillByName !== "undefined")
		{
			var possibleOptionElement = $(this.basename + " option:contains(\"" + prefillByName + "\")").first();

			if (possibleOptionElement.length > 0)
			{
				doFokus = true;
				this.picker.val(possibleOptionElement.val());
			}
		}

		var prefillById = this.picker.parent().data('prefill-by-id').toString();
		if (typeof prefillById !== "undefined")
		{
			doFokus = true;
			this.picker.val(prefillById);
		}

		if (doFokus)
		{
			this.picker.data('combobox').refresh();
			this.picker.trigger('change');

			this.$(this.picker.parent().data('next-input-selector').toString()).focus();
		}
	}

	initCombobox(selector)
	{
		this.hasCombobox = true;
		this.$(selector).combobox({
			appendId: '_text_input',
			bsVersion: '4',
			clearIfNoMatch: false
		});
	}

	GetPicker()
	{
		return this.picker;
	}

	GetInputElement()
	{
		return this.input_element;
	}

	GetValue()
	{
		return this.picker.val();
	}

	SetValue(value)
	{
		if (this.input_element != null)
		{
			this.input_element.val(value);
			this.input_element.trigger('change');
		}
		else
		{
			this.picker.val(value);
			this.picker.trigger("change");
		}
	}

	SetId(value)
	{
		this.picker.val(value);
		if (this.hasCombobox)
			this.picker.data('combobox').refresh();
		if (this.input_element != null)
			this.input_element.trigger('change');
		else
			this.picker.trigger("change");
	}

	Clear()
	{
		this.SetValue('');
		this.SetId(null);
	}
}

export default BasePicker;