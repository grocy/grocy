import BasePicker from "./BasePicker";

class locationpicker extends BasePicker
{
	constructor(Grocy, scopeSelector = null)
	{
		super(Grocy, "#location_id", scopeSelector);

		this.picker = this.$(this.basename);
		this.inputElement = this.$('#location_id_text_input');

		this.initCombobox('.location-combobox');

		this.prefill();
	}
}

export { locationpicker }