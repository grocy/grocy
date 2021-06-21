function userpicker(Grocy)
{

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

	var this_user_picker = Grocy.Components.UserPicker.GetPicker();
	var user_picker_doFocus = false;
	var possibleOptionElement = null;

	var prefillUser = this_user_picker.parent().data('prefill-by-username').toString();
	if (typeof prefillUser !== "undefined")
	{
		possibleOptionElement = $("#user_id option[data-additional-searchdata*=\"" + prefillUser + "\"]").first();
		if (possibleOptionElement.length === 0)
		{
			possibleOptionElement = $("#user_id option:contains(\"" + prefillUser + "\")").first();
		}

		if (possibleOptionElement.length > 0)
		{
			user_picker_doFocus = true;
			this_user_picker.val(possibleOptionElement.val());

		}
	}

	var prefillUserId = this_user_picker.parent().data('prefill-by-user-id').toString();
	if (typeof prefillUserId !== "undefined")
	{
		possibleOptionElement = $("#user_id option[value='" + prefillUserId + "']").first();
		if (possibleOptionElement.length > 0)
		{
			user_picker_doFocus = true;
			this_user_picker.val(possibleOptionElement.val());
		}
	}

	if (user_picker_doFocus)
	{
		this_user_picker.data('combobox').refresh();
		this_user_picker.trigger('change');

		$(this_user_picker.parent().data('next-input-selector').toString())
			.focus();
	}
}
export { userpicker }