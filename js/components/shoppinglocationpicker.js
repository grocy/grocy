import BasePicker from "./BasePicker";

class shoppinglocationpicker extends BasePicker
{

	constructor(Grocy, scopeSelector = null)
	{
		super(Grocy, "#shopping_location_id", scopeSelector);

		this.picker = this.$(this.basename);
		this.input_element = this.$(this.basename + '_text_input');

		this.initCombobox('.recipe-combobox');
		this.prefill();
	}
}

export { shoppinglocationpicker }