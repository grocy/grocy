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
	clearIfNoMatch: false
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

$('#recipe_id_text_input').on('blur', function(e)
{
	if ($('#recipe_id').hasClass("combobox-menu-visible"))
	{
		return;
	}

	var input = $('#recipe_id_text_input').val().toString();
	var possibleOptionElement = [];

	// Grocycode handling
	if (input.startsWith("grcy"))
	{
		var gc = input.split(":");
		if (gc[1] == "r")
		{
			possibleOptionElement = $("#recipe_id option[value=\"" + gc[2] + "\"]").first();
		}

		if (possibleOptionElement.length > 0)
		{
			$('#recipe_id').val(possibleOptionElement.val());
			$('#recipe_id').data('combobox').refresh();
			$('#recipe_id').trigger('change');
		}
		else
		{
			$('#recipe_id').val(null);
			$('#recipe_id_text_input').val("");
			$('#recipe_id').data('combobox').refresh();
			$('#recipe_id').trigger('change');
		}
	}
});

$(document).on("Grocy.BarcodeScanned", function(e, barcode, target)
{
	if (!(target == "@recipepicker" || target == "undefined" || target == undefined)) // Default target
	{
		return;
	}

	// Don't know why the blur event does not fire immediately ... this works...
	Grocy.Components.RecipePicker.GetInputElement().focusout();
	Grocy.Components.RecipePicker.GetInputElement().focus();
	Grocy.Components.RecipePicker.GetInputElement().blur();

	Grocy.Components.RecipePicker.GetInputElement().val(barcode);

	setTimeout(function()
	{
		Grocy.Components.RecipePicker.GetInputElement().focusout();
		Grocy.Components.RecipePicker.GetInputElement().focus();
		Grocy.Components.RecipePicker.GetInputElement().blur();
	}, Grocy.FormFocusDelay);
});
