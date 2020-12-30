Grocy.Components.ShoppingLocationPicker = {};

Grocy.Components.ShoppingLocationPicker.GetPicker = function()
{
	return $('#shopping_location_id');
}

Grocy.Components.ShoppingLocationPicker.GetInputElement = function()
{
	return $('#shopping_location_id_text_input');
}

Grocy.Components.ShoppingLocationPicker.GetValue = function()
{
	return $('#shopping_location_id').val();
}

Grocy.Components.ShoppingLocationPicker.SetValue = function(value)
{
	Grocy.Components.ShoppingLocationPicker.GetInputElement().val(value);
	Grocy.Components.ShoppingLocationPicker.GetInputElement().trigger('change');
}

Grocy.Components.ShoppingLocationPicker.SetId = function(value)
{
	Grocy.Components.ShoppingLocationPicker.GetPicker().val(value);
	Grocy.Components.ShoppingLocationPicker.GetPicker().data('combobox').refresh();
	Grocy.Components.ShoppingLocationPicker.GetInputElement().trigger('change');
}

Grocy.Components.ShoppingLocationPicker.Clear = function()
{
	Grocy.Components.ShoppingLocationPicker.SetValue('');
	Grocy.Components.ShoppingLocationPicker.SetId(null);
}

$('.shopping-location-combobox').combobox({
	appendId: '_text_input',
	bsVersion: '4',
	clearIfNoMatch: true
});

var prefillByName = Grocy.Components.ShoppingLocationPicker.GetPicker().parent().data('prefill-by-name').toString();
if (typeof prefillByName !== "undefined")
{
	possibleOptionElement = $("#shopping_location_id option:contains(\"" + prefillByName + "\")").first();

	if (possibleOptionElement.length > 0)
	{
		$('#shopping_location_id').val(possibleOptionElement.val());
		$('#shopping_location_id').data('combobox').refresh();
		$('#shopping_location_id').trigger('change');

		var nextInputElement = $(Grocy.Components.ShoppingLocationPicker.GetPicker().parent().data('next-input-selector').toString());
		nextInputElement.focus();
	}
}

var prefillById = Grocy.Components.ShoppingLocationPicker.GetPicker().parent().data('prefill-by-id').toString();
if (typeof prefillById !== "undefined")
{
	$('#shopping_location_id').val(prefillById);
	$('#shopping_location_id').data('combobox').refresh();
	$('#shopping_location_id').trigger('change');

	var nextInputElement = $(Grocy.Components.ShoppingLocationPicker.GetPicker().parent().data('next-input-selector').toString());
	nextInputElement.focus();
}
