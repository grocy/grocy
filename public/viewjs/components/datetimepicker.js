Grocy.Components.DateTimePicker = { };

Grocy.Components.DateTimePicker.GetInputElement = function()
{
	return $('.datetimepicker').find('input').not(".form-check-input");
}

Grocy.Components.DateTimePicker.GetValue = function()
{
	return Grocy.Components.DateTimePicker.GetInputElement().val();
}

Grocy.Components.DateTimePicker.SetValue = function(value)
{
	Grocy.Components.DateTimePicker.GetInputElement().val(value);
	Grocy.Components.DateTimePicker.GetInputElement().trigger('change');

	// "Click" the shortcut checkbox when the desired value is
	// not the shortcut value and it is currently set
	var shortcutValue = $("#datetimepicker-shortcut").data("datetimepicker-shortcut-value");
	if (value != shortcutValue && $("#datetimepicker-shortcut").is(":checked"))
	{
		$("#datetimepicker-shortcut").click();
	}

	Grocy.Components.DateTimePicker.GetInputElement().keyup();
}

Grocy.Components.DateTimePicker.Clear = function()
{
	$(".datetimepicker").datetimepicker("destroy");
	Grocy.Components.DateTimePicker.Init();

	Grocy.Components.DateTimePicker.GetInputElement().val("");

	// "Click" the shortcut checkbox when the desired value is
	// not the shortcut value and it is currently set
	value = "";
	var shortcutValue = $("#datetimepicker-shortcut").data("datetimepicker-shortcut-value");
	if (value != shortcutValue && $("#datetimepicker-shortcut").is(":checked"))
	{
		$("#datetimepicker-shortcut").click();
	}

	$('#datetimepicker-timeago').text('');
}

Grocy.Components.DateTimePicker.ChangeFormat = function(format)
{
	$(".datetimepicker").datetimepicker("destroy");
	Grocy.Components.DateTimePicker.GetInputElement().data("format", format);
	Grocy.Components.DateTimePicker.Init();

	if (format == "YYYY-MM-DD")
	{
		Grocy.Components.DateTimePicker.GetInputElement().addClass("date-only-datetimepicker");
	}
	else
	{
		Grocy.Components.DateTimePicker.GetInputElement().removeClass("date-only-datetimepicker");
	}
}

var startDate = null;
if (Grocy.Components.DateTimePicker.GetInputElement().data('init-with-now') === true)
{
	startDate = moment().format(Grocy.Components.DateTimePicker.GetInputElement().data('format'));
}
if (Grocy.Components.DateTimePicker.GetInputElement().data('init-value').length > 0)
{
	startDate = moment(Grocy.Components.DateTimePicker.GetInputElement().data('init-value')).format(Grocy.Components.DateTimePicker.GetInputElement().data('format'));
}

var limitDate = moment('2999-12-31 23:59:59');
if (Grocy.Components.DateTimePicker.GetInputElement().data('limit-end-to-now') === true)
{
	limitDate = moment();
}

Grocy.Components.DateTimePicker.Init = function()
{
	$('.datetimepicker').datetimepicker(
	{
		format: Grocy.Components.DateTimePicker.GetInputElement().data('format'),
		buttons: {
			showToday: true,
			showClose: true
		},
		calendarWeeks: Grocy.CalendarShowWeekNumbers,
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
}
Grocy.Components.DateTimePicker.Init();

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
		var date = moment((new Date()).getFullYear().toString() + value);
		if (date.isBefore(moment()))
		{
			date.add(1, "year");
		}
		Grocy.Components.DateTimePicker.SetValue(date.format(format));
		nextInputElement.focus();
	}
	else if (value.length === 8 && $.isNumeric(value))
	{
		Grocy.Components.DateTimePicker.SetValue(value.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
		nextInputElement.focus();
	}
	else if (value.length === 7 && $.isNumeric(value.substring(0, 6)) && (value.substring(6, 7).toLowerCase() === "e" || value.substring(6, 7).toLowerCase() === "+"))
	{
		var date = moment(value.substring(0, 4) + "-" + value.substring(4, 6) + "-01").endOf("month");
		Grocy.Components.DateTimePicker.SetValue(date.format(format));
		nextInputElement.focus();
	}
	else
	{
		var dateObj = moment(value, format, true);
		if (dateObj.isValid())
		{
			if (e.shiftKey)
			{
				// WITH shift modifier key

				if (e.keyCode === 38) // Up
				{
					Grocy.Components.DateTimePicker.SetValue(dateObj.add(-1, 'months').format(format));
				}
				else if (e.keyCode === 40) // Down
				{
					Grocy.Components.DateTimePicker.SetValue(dateObj.add(1, 'months').format(format));
				}
				else if (e.keyCode === 37) // Left
				{
					Grocy.Components.DateTimePicker.SetValue(dateObj.add(-1, 'years').format(format));
				}
				else if (e.keyCode === 39) // Right
				{
					Grocy.Components.DateTimePicker.SetValue(dateObj.add(1, 'years').format(format));
				}
			}
			else
			{
				// WITHOUT shift modifier key

				if (e.keyCode === 38) // Up
				{
					Grocy.Components.DateTimePicker.SetValue(dateObj.add(-1, 'days').format(format));
				}
				else if (e.keyCode === 40) // Down
				{
					Grocy.Components.DateTimePicker.SetValue(dateObj.add(1, 'days').format(format));
				}
				else if (e.keyCode === 37) // Left
				{
					Grocy.Components.DateTimePicker.SetValue(dateObj.add(-1, 'weeks').format(format));
				}
				else if (e.keyCode === 39) // Right
				{
					Grocy.Components.DateTimePicker.SetValue(dateObj.add(1, 'weeks').format(format));
				}
			}
		}
	}

	//Custom validation
	value = Grocy.Components.DateTimePicker.GetValue();
	dateObj = moment(value, format, true);
	var element = Grocy.Components.DateTimePicker.GetInputElement()[0];
	if (!dateObj.isValid())
	{
		if ($(element).hasAttr("required"))
		{
			element.setCustomValidity("error");
		}		
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

		var earlierThanLimit = Grocy.Components.DateTimePicker.GetInputElement().data("earlier-than-limit");
		if (!earlierThanLimit.isEmpty())
		{
			if (moment(value).isBefore(moment(earlierThanLimit)))
			{
				$("#datetimepicker-earlier-than-info").removeClass("d-none");
			}
			else
			{
				$("#datetimepicker-earlier-than-info").addClass("d-none");
			}
		}
	}

	// "Click" the shortcut checkbox when the shortcut value was
	// entered manually and it is currently not set
	var shortcutValue = $("#datetimepicker-shortcut").data("datetimepicker-shortcut-value");
	if (value == shortcutValue && !$("#datetimepicker-shortcut").is(":checked"))
	{
		$("#datetimepicker-shortcut").click();
	}
});

Grocy.Components.DateTimePicker.GetInputElement().on('input', function(e)
{
	$('#datetimepicker-timeago').attr("datetime", Grocy.Components.DateTimePicker.GetValue());
	EmptyElementWhenMatches('#datetimepicker-timeago', __t('timeago_nan'));
	RefreshContextualTimeago("#datetimepicker-wrapper");
});

$('.datetimepicker').on('update.datetimepicker', function(e)
{
	Grocy.Components.DateTimePicker.GetInputElement().trigger('input');
	Grocy.Components.DateTimePicker.GetInputElement().trigger('change');
	Grocy.Components.DateTimePicker.GetInputElement().trigger('keypress');
	Grocy.Components.DateTimePicker.GetInputElement().trigger('keyup');
});

$('.datetimepicker').on('hide.datetimepicker', function(e)
{
	Grocy.Components.DateTimePicker.GetInputElement().trigger('input');
	Grocy.Components.DateTimePicker.GetInputElement().trigger('change');
	Grocy.Components.DateTimePicker.GetInputElement().trigger('keypress');
	Grocy.Components.DateTimePicker.GetInputElement().trigger('keyup');
});

$("#datetimepicker-shortcut").on("click", function()
{
	if (this.checked)
	{
		var value = $("#datetimepicker-shortcut").data("datetimepicker-shortcut-value");
		Grocy.Components.DateTimePicker.SetValue(value);
		Grocy.Components.DateTimePicker.GetInputElement().attr("readonly", "");
		$(Grocy.Components.DateTimePicker.GetInputElement().data('next-input-selector')).focus();
	}
	else
	{
		Grocy.Components.DateTimePicker.SetValue("");
		Grocy.Components.DateTimePicker.GetInputElement().removeAttr("readonly");
		Grocy.Components.DateTimePicker.GetInputElement().focus();
	}

	Grocy.Components.DateTimePicker.GetInputElement().trigger('input');
	Grocy.Components.DateTimePicker.GetInputElement().trigger('change');
	Grocy.Components.DateTimePicker.GetInputElement().trigger('keypress');
});
