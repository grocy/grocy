Grocy.Components.RecipePicker = { };

Grocy.Components.RecipePicker.GetPicker = function()
{
	return $('#recipe_id');
}

Grocy.Components.RecipePicker.GetInputElement = function()
{
	return $('#recipe_id_text_input');
}

Grocy.Components.RecipePicker.GetValue = function()
{
	return $('#recipe_id').val();
}

Grocy.Components.RecipePicker.SetValue = function(value)
{
	Grocy.Components.RecipePicker.GetInputElement().val(value);
	Grocy.Components.RecipePicker.GetInputElement().trigger('change');
}

Grocy.Components.RecipePicker.SetId = function(value)
{
	Grocy.Components.RecipePicker.GetPicker().val(value);
	Grocy.Components.RecipePicker.GetPicker().data('combobox').refresh();
	Grocy.Components.RecipePicker.GetInputElement().trigger('change');
}

Grocy.Components.RecipePicker.Clear = function()
{
	Grocy.Components.RecipePicker.SetValue('');
	Grocy.Components.RecipePicker.SetId(null);
}

$('.recipe-combobox').combobox({
	appendId: '_text_input',
	bsVersion: '4',
	clearIfNoMatch: true
});

var prefillByName = Grocy.Components.RecipePicker.GetPicker().parent().data('prefill-by-name').toString();
if (typeof prefillByName !== "undefined")
{
	possibleOptionElement = $("#recipe_id option:contains(\"" + prefillByName + "\")").first();

	if (possibleOptionElement.length > 0)
	{
		$('#recipe_id').val(possibleOptionElement.val());
		$('#recipe_id').data('combobox').refresh();
		$('#recipe_id').trigger('change');

		var nextInputElement = $(Grocy.Components.RecipePicker.GetPicker().parent().data('next-input-selector').toString());
		nextInputElement.focus();
	}
}

var prefillById = Grocy.Components.RecipePicker.GetPicker().parent().data('prefill-by-id').toString();
if (typeof prefillById !== "undefined")
{
	$('#recipe_id').val(prefillById);
	$('#recipe_id').data('combobox').refresh();
	$('#recipe_id').trigger('change');

	var nextInputElement = $(Grocy.Components.RecipePicker.GetPicker().parent().data('next-input-selector').toString());
	nextInputElement.focus();
}
