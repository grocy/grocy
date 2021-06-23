import BasePicker from "./BasePicker";

class userpicker extends BasePicker
{
	constructor(Grocy, scopeSelector = null)
	{
		super(Grocy, "#user_id", scopeSelector);

		this.picker = this.$(this.basename);
		this.input_element = this.$(this.basename + '_text_input');

		this.initCombobox('.user-combobox');
		this.prefill();
	}

	prefill()
	{
		var doFocus = false;
		var possibleOptionElement = null;

		var prefillUser = this.picker.parent().data('prefill-by-username').toString();
		if (typeof prefillUser !== "undefined")
		{
			possibleOptionElement = this.$("#user_id option[data-additional-searchdata*=\"" + prefillUser + "\"]").first();
			if (possibleOptionElement.length === 0)
			{
				possibleOptionElement = this.$("#user_id option:contains(\"" + prefillUser + "\")").first();
			}

			if (possibleOptionElement.length > 0)
			{
				doFocus = true;
				this.picker.val(possibleOptionElement.val());
			}
		}

		var prefillUserId = this.picker.parent().data('prefill-by-user-id').toString();
		if (typeof prefillUserId !== "undefined")
		{
			possibleOptionElement = this.$("#user_id option[value='" + prefillUserId + "']").first();
			if (possibleOptionElement.length > 0)
			{
				doFocus = true;
				this.picker.val(possibleOptionElement.val());
			}
		}

		if (doFocus)
		{
			this.picker.data('combobox').refresh();
			this.picker.trigger('change');

			this.$(this.picker.parent().data('next-input-selector').toString())
				.focus();
		}
	}
}
export { userpicker }