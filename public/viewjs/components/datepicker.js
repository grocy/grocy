$(function()
{
	$('.datepicker').datepicker(
	{
		format: 'yyyy-mm-dd',
		startDate: '+0d',
		todayHighlight: true,
		autoclose: true,
		calendarWeeks: true,
		orientation: 'bottom auto',
		weekStart: 1,
		showOnFocus: false,
		language: L('bootstrap_datepicker_locale')
	});
	$('.datepicker').trigger('change');

	EmptyElementWhenMatches('#datepicker-timeago', L('timeago_nan'));
});

$('.datepicker').on('keydown', function(e)
{
	if (e.keyCode === 13) //Enter
	{
		$('.datepicker').trigger('change');
	}
});

$('.datepicker').on('keypress', function(e)
{
	var element = $(e.target);
	var value = element.val();
	var dateObj = moment(element.val(), 'YYYY-MM-DD', true);

	$('.datepicker').datepicker('hide');

	//If input is empty and any arrow key is pressed, set date to today
	if (value.length === 0 && (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 37 || e.keyCode === 39))
	{
		dateObj = moment(new Date(), 'YYYY-MM-DD', true);
	}

	if (dateObj.isValid())
	{
		if (e.keyCode === 38) //Up
		{
			element.val(dateObj.add(-1, 'days').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 40) //Down
		{
			element.val(dateObj.add(1, 'days').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 37) //Left
		{
			element.val(dateObj.add(-1, 'weeks').format('YYYY-MM-DD'));
		}
		else if (e.keyCode === 39) //Right
		{
			element.val(dateObj.add(1, 'weeks').format('YYYY-MM-DD'));
		}
	}
});

$('.datepicker').on('change', function(e)
{
	var value = $('.datepicker').val();
	var now = new Date();
	var centuryStart = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '00');
	var centuryEnd = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '99');

	if (value === 'x' || value === 'X') {
		value = '29991231';
	}

	if (value.length === 4 && !(Number.parseInt(value) > centuryStart && Number.parseInt(value) < centuryEnd))
	{
		value = (new Date()).getFullYear().toString() + value;
	}

	if (value.length === 8 && $.isNumeric(value))
	{
		value = value.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3');
		$('.datepicker').val(value);
	}

	$('#datepicker-timeago').text($.timeago($('.datepicker').val()));
	EmptyElementWhenMatches('#datepicker-timeago', L('timeago_nan'));
});

$('#datepicker-button').on('click', function(e)
{
	$('.datepicker').datepicker('show');
});
