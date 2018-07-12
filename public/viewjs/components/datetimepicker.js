Grocy.Components.DateTimePicker = { };

Grocy.Components.DateTimePicker.GetInputElement = function()
{
	return $('.datetimepicker').find('input');
}

Grocy.Components.DateTimePicker.GetValue = function()
{
	return Grocy.Components.DateTimePicker.GetInputElement().val();
}

Grocy.Components.DateTimePicker.SetValue = function(value)
{
	Grocy.Components.DateTimePicker.GetInputElement().val(value);
	Grocy.Components.DateTimePicker.GetInputElement().trigger('change');
}

var startDate = null;
if (Grocy.Components.DateTimePicker.GetInputElement().data('init-with-now') === true)
{
	startDate = moment().format(Grocy.Components.DateTimePicker.GetInputElement().data('format'));
}

var limitDate = moment('2999-12-31 23:59:59');
if (Grocy.Components.DateTimePicker.GetInputElement().data('limit-end-to-now') === true)
{
	limitDate = moment();
}

$('.datetimepicker').datetimepicker(
{
	format: Grocy.Components.DateTimePicker.GetInputElement().data('format'),
	buttons: {
		showToday: true,
		showClose: true
	},
	calendarWeeks: true,
	maxDate: limitDate,
	locale: moment.locale(),
	defaultDate: startDate,
	useCurrent: false,
	icons: {
		time: 'far fa-clock',
		date: 'far fa-calendar',
		up: 'fas fa-arrow-up',
		down: 'fas fa-arrow-down',
		previous: 'fas fa-chevron-left',
		next: 'fas fa-chevron-right',
		today: 'fas fa-calendar-check',
		clear: 'far fa-trash-alt',
		close: 'far fa-times-circle'
	},
	sideBySide: true,
	keyBinds: {
		up: function(widget) { },
		down: function(widget) { },
		'control up': function(widget) { },
		'control down': function(widget) { },
		left: function(widget) { },
		right: function(widget) { },
		pageUp: function(widget) { },
		pageDown: function(widget) { },
		enter: function(widget) { },
		escape: function(widget) { },
		'control space': function(widget) { },
		t: function(widget) { },
		'delete': function(widget) { }
	}
});

Grocy.Components.DateTimePicker.GetInputElement().on('keyup', function(e)
{
	$('.datetimepicker').datetimepicker('hide');

	var value = Grocy.Components.DateTimePicker.GetValue();
	var now = new Date();
	var centuryStart = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '00');
	var centuryEnd = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '99');
	var format = Grocy.Components.DateTimePicker.GetInputElement().data('format');
	var nextInputElement = $(Grocy.Components.DateTimePicker.GetInputElement().data('next-input-selector'));

	//If input is empty and any arrow key is pressed, set date to today
	if (value.length === 0 && (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 37 || e.keyCode === 39))
	{
		Grocy.Components.DateTimePicker.SetValue(moment(new Date(), format, true).format(format));
		nextInputElement.focus();
	}
	else if (value === 'x' || value === 'X')
	{
		Grocy.Components.DateTimePicker.SetValue(moment('2999-12-31 23:59:59').format(format));
		nextInputElement.focus();
	}
	else if (value.length === 4 && !(Number.parseInt(value) > centuryStart && Number.parseInt(value) < centuryEnd))
	{
		Grocy.Components.DateTimePicker.SetValue((new Date()).getFullYear().toString() + value);
		nextInputElement.focus();
	}
	else if (value.length === 8 && $.isNumeric(value))
	{
		Grocy.Components.DateTimePicker.SetValue(value.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
		nextInputElement.focus();
	}
	else
	{
		var dateObj = moment(value, format, true);
		if (dateObj.isValid())
		{
			if (e.keyCode === 38) //Up
			{
				Grocy.Components.DateTimePicker.SetValue(dateObj.add(-1, 'days').format(format));
			}
			else if (e.keyCode === 40) //Down
			{
				Grocy.Components.DateTimePicker.SetValue(dateObj.add(1, 'days').format(format));
			}
			else if (e.keyCode === 37) //Left
			{
				Grocy.Components.DateTimePicker.SetValue(dateObj.add(-1, 'weeks').format(format));
			}
			else if (e.keyCode === 39) //Right
			{
				Grocy.Components.DateTimePicker.SetValue(dateObj.add(1, 'weeks').format(format));
			}
		}
	}

	//Custom validation
	value = Grocy.Components.DateTimePicker.GetValue();
	dateObj = moment(value, format, true);
	var element = Grocy.Components.DateTimePicker.GetInputElement()[0];
	if (!dateObj.isValid())
	{
		element.setCustomValidity("error");
	}
	else
	{
		if (Grocy.Components.DateTimePicker.GetInputElement().data('limit-end-to-now') === true && dateObj.isAfter(moment()))
		{
			element.setCustomValidity("error");
		}
		else if (Grocy.Components.DateTimePicker.GetInputElement().data('limit-start-to-now') === true && dateObj.isBefore(moment()))
		{
			element.setCustomValidity("error");
		}
		else
		{
			element.setCustomValidity("");
		}
	}
});

Grocy.Components.DateTimePicker.GetInputElement().on('input', function(e)
{
	$('#datetimepicker-timeago').text($.timeago(Grocy.Components.DateTimePicker.GetValue()));
	EmptyElementWhenMatches('#datetimepicker-timeago', L('timeago_nan'));
});

$('.datetimepicker').on('update.datetimepicker', function(e)
{
	Grocy.Components.DateTimePicker.GetInputElement().trigger('input');
});
