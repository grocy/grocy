Grocy.Components.UserPicker = {};

Grocy.Components.UserPicker.GetPicker = function()
{
	return $('#user_id');
}

Grocy.Components.UserPicker.GetInputElement = function()
{
	return $('#user_id_text_input');
}

Grocy.Components.UserPicker.GetValue = function()
{
	return $('#user_id').val();
}

Grocy.Components.UserPicker.SetValue = function(value)
{
	Grocy.Components.UserPicker.GetInputElement().val(value);
	Grocy.Components.UserPicker.GetInputElement().trigger('change');
}

Grocy.Components.UserPicker.SetId = function(value)
{
	Grocy.Components.UserPicker.GetPicker().val(value);
	Grocy.Components.UserPicker.GetPicker().data('combobox').refresh();
	Grocy.Components.UserPicker.GetInputElement().trigger('change');
}

Grocy.Components.UserPicker.Clear = function()
{
	Grocy.Components.UserPicker.SetValue('');
	Grocy.Components.UserPicker.SetId(null);
}

$('.user-combobox').combobox({
	appendId: '_text_input',
	bsVersion: '4'
});

var prefillUser = Grocy.Components.UserPicker.GetPicker().parent().data('prefill-by-username').toString();
if (typeof prefillUser !== "undefined")
{
	var possibleOptionElement = $("#user_id option[data-additional-searchdata*=\"" + prefillUser + "\"]").first();
	if (possibleOptionElement.length === 0)
	{
		possibleOptionElement = $("#user_id option:contains(\"" + prefillUser + "\")").first();
	}

	if (possibleOptionElement.length > 0)
	{
		$('#user_id').val(possibleOptionElement.val());
		$('#user_id').data('combobox').refresh();
		$('#user_id').trigger('change');

		var nextInputElement = $(Grocy.Components.UserPicker.GetPicker().parent().data('next-input-selector').toString());
		nextInputElement.focus();
	}
}

var prefillUserId = Grocy.Components.UserPicker.GetPicker().parent().data('prefill-by-user-id').toString();
if (typeof prefillUserId !== "undefined")
{
	var possibleOptionElement = $("#user_id option[value='" + prefillUserId + "']").first();
	if (possibleOptionElement.length > 0)
	{
		$('#user_id').val(possibleOptionElement.val());
		$('#user_id').data('combobox').refresh();
		$('#user_id').trigger('change');

		var nextInputElement = $(Grocy.Components.UserPicker.GetPicker().parent().data('next-input-selector').toString());
		nextInputElement.focus();
	}
}
