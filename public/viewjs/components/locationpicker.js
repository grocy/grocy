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

var prefillByName = Grocy.Components.LocationPicker.GetPicker().parent().data('prefill-by-name').toString();
if (typeof prefillByName !== "undefined")
{
	possibleOptionElement = $("#location_id option:contains(\"" + prefillByName + "\")").first();

	if (possibleOptionElement.length > 0)
	{
		$('#location_id').val(possibleOptionElement.val());
		$('#location_id').data('combobox').refresh();
		$('#location_id').trigger('change');

		var nextInputElement = $(Grocy.Components.LocationPicker.GetPicker().parent().data('next-input-selector').toString());
		nextInputElement.focus();
	}
}

var prefillById = Grocy.Components.LocationPicker.GetPicker().parent().data('prefill-by-id').toString();
if (typeof prefillById !== "undefined")
{
	$('#location_id').val(prefillById);
	$('#location_id').data('combobox').refresh();
	$('#location_id').trigger('change');

	var nextInputElement = $(Grocy.Components.LocationPicker.GetPicker().parent().data('next-input-selector').toString());
	nextInputElement.focus();
}
