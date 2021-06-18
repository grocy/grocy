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

var shoppinglocationpicker_doFocus = false;
var this_shoppinglocation_picker = $('#shopping_location_id');

var prefillByName = this_shoppinglocation_picker.parent().data('prefill-by-name').toString();
if (typeof prefillByName !== "undefined")
{
	var possibleOptionElement = $("#shopping_location_id option:contains(\"" + prefillByName + "\")").first();

	if (possibleOptionElement.length > 0)
	{
		this_shoppinglocation_picker.val(possibleOptionElement.val());
		shoppinglocationpicker_doFocus = true;
	}
}

var prefillById = this_shoppinglocation_picker.parent().data('prefill-by-id').toString();
if (typeof prefillById !== "undefined")
{
	this_shoppinglocation_picker.val(prefillById);
	shoppinglocationpicker_doFocus = true;
}

if (shoppinglocationpicker_doFocus)
{
	this_shoppinglocation_picker.data('combobox').refresh();
	this_shoppinglocation_picker.trigger('change');

	$(this_shoppinglocation_picker.parent().data('next-input-selector').toString())
		.focus();
}