import BasePicker from './BasePicker'

class recipepicker extends BasePicker
{

	constructor(Grocy, scopeSelector = null)
	{
		super(Grocy, "#recipe_id", scopeSelector);

		this.picker = this.$(this.basename);
		this.input_element = this.$(this.basename + '_text_input');

		this.initCombobox('.recipe-combobox');
		this.prefill();
	}
}

export { recipepicker }