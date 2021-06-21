function locationpicker(Grocy)
{

	Grocy.Components.LocationPicker = {};

	Grocy.Components.LocationPicker.GetPicker = function()
	{
		return $('#location_id');
	}

	Grocy.Components.LocationPicker.GetInputElement = function()
	{
		return $('#location_id_text_input');
	}

	Grocy.Components.LocationPicker.GetValue = function()
	{
		return $('#location_id').val();
	}

	Grocy.Components.LocationPicker.SetValue = function(value)
	{
		Grocy.Components.LocationPicker.GetInputElement().val(value);
		Grocy.Components.LocationPicker.GetInputElement().trigger('change');
	}

	Grocy.Components.LocationPicker.SetId = function(value)
	{
		Grocy.Components.LocationPicker.GetPicker().val(value);
		Grocy.Components.LocationPicker.GetPicker().data('combobox').refresh();
		Grocy.Components.LocationPicker.GetInputElement().trigger('change');
	}

	Grocy.Components.LocationPicker.Clear = function()
	{
		Grocy.Components.LocationPicker.SetValue('');
		Grocy.Components.LocationPicker.SetId(null);
	}

	$('.location-combobox').combobox({
		appendId: '_text_input',
		bsVersion: '4',
		clearIfNoMatch: true
	});

	// these names seem a bit long, but as they live in global space
	// and this is a component, they need to be unique.
	var locationpicker_doFocus = false;
	var this_location_picker = Grocy.Components.LocationPicker.GetPicker();

	var prefillByName = this_location_picker.parent().data('prefill-by-name').toString();
	if (typeof prefillByName !== "undefined")
	{
		var possibleOptionElement = $("#location_id option:contains(\"" + prefillByName + "\")").first();

		if (possibleOptionElement.length > 0)
		{
			locationpicker_doFocus = true;
			this_location_picker.val(possibleOptionElement.val());
		}
	}

	var prefillById = this_location_picker.parent().data('prefill-by-id').toString();
	if (typeof prefillById !== "undefined")
	{
		locationpicker_doFocus = true;
		this_location_picker.val(prefillById);
	}

	if (locationpicker_doFocus)
	{
		this_location_picker.data('combobox').refresh();
		this_location_picker.trigger('change');

		$(this_location_picker.parent().data('next-input-selector').toString())
			.focus();
	}
}

export { locationpicker }