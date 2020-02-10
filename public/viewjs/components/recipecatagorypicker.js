Grocy.Components.RecipeCatagoryPicker = { };

Grocy.Components.RecipeCatagoryPicker.GetPicker = function()
{
	return $('#recipe_catagory_id');
}

Grocy.Components.RecipeCatagoryPicker.GetInputElement = function()
{
	return $('#recipe_catagory_id_text_input');
}

Grocy.Components.RecipeCatagoryPicker.GetValue = function()
{
	return $('#recipe_catagory_id').val();
}

Grocy.Components.RecipeCatagoryPicker.SetValue = function(value)
{
	Grocy.Components.RecipeCatagoryPicker.GetInputElement().val(value);
	Grocy.Components.RecipeCatagoryPicker.GetInputElement().trigger('change');
}

Grocy.Components.RecipeCatagoryPicker.SetId = function(value)
{
	Grocy.Components.RecipeCatagoryPicker.GetPicker().val(value);
	Grocy.Components.RecipeCatagoryPicker.GetPicker().data('combobox').refresh();
	Grocy.Components.RecipeCatagoryPicker.GetInputElement().trigger('change');
}

Grocy.Components.RecipeCatagoryPicker.Clear = function()
{
	Grocy.Components.RecipeCatagoryPicker.SetValue('');
	Grocy.Components.RecipeCatagoryPicker.SetId(null);
}

$('.recipe-catagory-combobox').combobox({
	appendId: '_text_input',
	bsVersion: '4',
	clearIfNoMatch: true
});

var prefillByName = Grocy.Components.RecipeCatagoryPicker.GetPicker().parent().data('prefill-by-name').toString();
if (typeof prefillByName !== "undefined")
{
	possibleOptionElement = $("#recipe_catagory_id option:contains(\"" + prefillByName + "\")").first();

	if (possibleOptionElement.length > 0)
	{
		$('#recipe_catagory_id').val(possibleOptionElement.val());
		$('#recipe_catagory_id').data('combobox').refresh();
		$('#recipe_catagory_id').trigger('change');

		var nextInputElement = $(Grocy.Components.RecipeCatagoryPicker.GetPicker().parent().data('next-input-selector').toString());
		nextInputElement.focus();
	}
}

var prefillById = Grocy.Components.RecipeCatagoryPicker.GetPicker().parent().data('prefill-by-id').toString();
if (typeof prefillById !== "undefined")
{
	$('#recipe_catagory_id').val(prefillById);
	$('#recipe_catagory_id').data('combobox').refresh();
	$('#recipe_catagory_id').trigger('change');

	var nextInputElement = $(Grocy.Components.RecipeCatagoryPicker.GetPicker().parent().data('next-input-selector').toString());
	nextInputElement.focus();
}
