$(".numberpicker-down-button").unbind('click').on("click", function ()
{
	var inputElement = $(this).parent().parent().find('input[type="number"]')[0];
	inputElement.stepDown();
	$(inputElement).trigger('change');
});

$(".numberpicker-up-button").unbind('click').on("click", function()
{
	var inputElement = $(this).parent().parent().find('input[type="number"]')[0];
	inputElement.stepUp();
	$(inputElement).trigger('change');
});
