function recipepicker(Grocy)
{

	Grocy.Components.RecipePicker = {};

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

	var this_recipe_picker = Grocy.Components.RecipePicker.GetPicker();
	var recipe_picker_doFocus = false;

	var prefillByName = this_recipe_picker.parent().data('prefill-by-name').toString();
	if (typeof prefillByName !== "undefined")
	{
		var possibleOptionElement = $("#recipe_id option:contains(\"" + prefillByName + "\")").first();

		if (possibleOptionElement.length > 0)
		{
			recipe_picker_doFocus = true;
			this_recipe_picker.val(possibleOptionElement.val());
		}
	}

	var prefillById = this_recipe_picker.parent().data('prefill-by-id').toString();
	if (typeof prefillById !== "undefined")
	{
		recipe_picker_doFocus = true;
		this_recipe_picker.val(prefillById);
	}

	if (recipe_picker_doFocus)
	{
		this_recipe_picker.data('combobox').refresh();
		this_recipe_picker.trigger('change');

		$(this_recipe_picker.parent().data('next-input-selector').toString()).focus();
	}
}

export { recipepicker }