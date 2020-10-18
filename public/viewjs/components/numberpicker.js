$(".numberpicker-down-button").unbind('click').on("click", function()
{
	var inputElement = $(this).parent().parent().find('input[type="number"]');
	inputElement.val(parseFloat(inputElement.val()) - 1);
	inputElement.trigger('keyup');
	inputElement.trigger('change');
});

$(".numberpicker-up-button").unbind('click').on("click", function()
{
	var inputElement = $(this).parent().parent().find('input[type="number"]');
	inputElement.val(parseFloat(inputElement.val()) + 1);
	inputElement.trigger('keyup');
	inputElement.trigger('change');
});

$(".numberpicker").on("keyup", function()
{
	if ($(this).data("not-equal") && !$(this).data("not-equal").toString().isEmpty() && $(this).data("not-equal") == $(this).val())
	{
		$(this)[0].setCustomValidity("error");
	}
	else
	{
		$(this)[0].setCustomValidity("");
	}
});
