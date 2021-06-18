Grocy.Components.DateTimePicker2 = {};

Grocy.Components.DateTimePicker2.GetInputElement = function()
{
	return $('.datetimepicker2').find('input').not(".form-check-input");
}

Grocy.Components.DateTimePicker2.GetValue = function()
{
	return Grocy.Components.DateTimePicker2.GetInputElement().val();
}

Grocy.Components.DateTimePicker2.SetValue = function(value)
{
	// "Click" the shortcut checkbox when the desired value is
	// not the shortcut value and it is currently set
	var shortcutValue = $("#datetimepicker2-shortcut").data("datetimepicker2-shortcut-value");
	if (value != shortcutValue && $("#datetimepicker2-shortcut").is(":checked"))
	{
		$("#datetimepicker2-shortcut").click();
	}

	Grocy.Components.DateTimePicker2.GetInputElement().val(value);
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('change');

	Grocy.Components.DateTimePicker2.GetInputElement().keyup();
}

Grocy.Components.DateTimePicker2.Clear = function()
{
	$(".datetimepicker2").datetimepicker("destroy");
	Grocy.Components.DateTimePicker2.Init();

	Grocy.Components.DateTimePicker2.GetInputElement().val("");

	// "Click" the shortcut checkbox when the desired value is
	// not the shortcut value and it is currently set
	value = "";
	var shortcutValue = $("#datetimepicker2-shortcut").data("datetimepicker2-shortcut-value");
	if (value != shortcutValue && $("#datetimepicker2-shortcut").is(":checked"))
	{
		$("#datetimepicker2-shortcut").click();
	}

	$('#datetimepicker2-timeago').text('');
}

Grocy.Components.DateTimePicker2.ChangeFormat = function(format)
{
	$(".datetimepicker2").datetimepicker("destroy");
	Grocy.Components.DateTimePicker2.GetInputElement().data("format", format);
	Grocy.Components.DateTimePicker2.Init();

	if (format == "YYYY-MM-DD")
	{
		Grocy.Components.DateTimePicker2.GetInputElement().addClass("date-only-datetimepicker");
	}
	else
	{
		Grocy.Components.DateTimePicker2.GetInputElement().removeClass("date-only-datetimepicker");
	}
}

var startDate = null;
if (Grocy.Components.DateTimePicker2.GetInputElement().data('init-with-now') === true)
{
	startDate = moment().format(Grocy.Components.DateTimePicker2.GetInputElement().data('format'));
}
if (Grocy.Components.DateTimePicker2.GetInputElement().data('init-value').length > 0)
{
	startDate = moment(Grocy.Components.DateTimePicker2.GetInputElement().data('init-value')).format(Grocy.Components.DateTimePicker2.GetInputElement().data('format'));
}

var limitDate = moment('2999-12-31 23:59:59');
if (Grocy.Components.DateTimePicker2.GetInputElement().data('limit-end-to-now') === true)
{
	limitDate = moment();
}

Grocy.Components.DateTimePicker2.Init = function()
{
	$('.datetimepicker2').datetimepicker(
		{
			format: Grocy.Components.DateTimePicker2.GetInputElement().data('format'),
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
Grocy.Components.DateTimePicker2.Init();

Grocy.Components.DateTimePicker2.GetInputElement().on('keyup', function(e)
{
	$('.datetimepicker2').datetimepicker('hide');

	var value = Grocy.Components.DateTimePicker2.GetValue();
	var now = new Date();
	var centuryStart = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '00');
	var centuryEnd = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '99');
	var format = Grocy.Components.DateTimePicker2.GetInputElement().data('format');
	var nextInputElement = $(Grocy.Components.DateTimePicker2.GetInputElement().data('next-input-selector'));

	//If input is empty and any arrow key is pressed, set date to today
	if (value.length === 0 && (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 37 || e.keyCode === 39))
	{
		Grocy.Components.DateTimePicker2.SetValue(moment(new Date(), format, true).format(format));
		nextInputElement.focus();
	}
	else if (value === 'x' || value === 'X')
	{
		Grocy.Components.DateTimePicker2.SetValue(moment('2999-12-31 23:59:59').format(format));
		nextInputElement.focus();
	}
	else if (value.length === 4 && !(Number.parseInt(value) > centuryStart && Number.parseInt(value) < centuryEnd))
	{
		var date = moment((new Date()).getFullYear().toString() + value);
		if (date.isBefore(moment()))
		{
			date.add(1, "year");
		}
		Grocy.Components.DateTimePicker2.SetValue(date.format(format));
		nextInputElement.focus();
	}
	else if (value.length === 8 && $.isNumeric(value))
	{
		Grocy.Components.DateTimePicker2.SetValue(value.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
		nextInputElement.focus();
	}
	else if (value.length === 7 && $.isNumeric(value.substring(0, 6)) && (value.substring(6, 7).toLowerCase() === "e" || value.substring(6, 7).toLowerCase() === "+"))
	{
		var date = moment(value.substring(0, 4) + "-" + value.substring(4, 6) + "-01").endOf("month");
		Grocy.Components.DateTimePicker2.SetValue(date.format(format));
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
					Grocy.Components.DateTimePicker2.SetValue(dateObj.add(-1, 'months').format(format));
				}
				else if (e.keyCode === 40) // Down
				{
					Grocy.Components.DateTimePicker2.SetValue(dateObj.add(1, 'months').format(format));
				}
				else if (e.keyCode === 37) // Left
				{
					Grocy.Components.DateTimePicker2.SetValue(dateObj.add(-1, 'years').format(format));
				}
				else if (e.keyCode === 39) // Right
				{
					Grocy.Components.DateTimePicker2.SetValue(dateObj.add(1, 'years').format(format));
				}
			}
			else
			{
				// WITHOUT shift modifier key

				if (e.keyCode === 38) // Up
				{
					Grocy.Components.DateTimePicker2.SetValue(dateObj.add(-1, 'days').format(format));
				}
				else if (e.keyCode === 40) // Down
				{
					Grocy.Components.DateTimePicker2.SetValue(dateObj.add(1, 'days').format(format));
				}
				else if (e.keyCode === 37) // Left
				{
					Grocy.Components.DateTimePicker2.SetValue(dateObj.add(-1, 'weeks').format(format));
				}
				else if (e.keyCode === 39) // Right
				{
					Grocy.Components.DateTimePicker2.SetValue(dateObj.add(1, 'weeks').format(format));
				}
			}
		}
	}

	//Custom validation
	value = Grocy.Components.DateTimePicker2.GetValue();
	dateObj = moment(value, format, true);
	var element = Grocy.Components.DateTimePicker2.GetInputElement()[0];
	if (!dateObj.isValid())
	{
		if ($(element).hasAttr("required"))
		{
			element.setCustomValidity("error");
		}
	}
	else
	{
		if (Grocy.Components.DateTimePicker2.GetInputElement().data('limit-end-to-now') === true && dateObj.isAfter(moment()))
		{
			element.setCustomValidity("error");
		}
		else if (Grocy.Components.DateTimePicker2.GetInputElement().data('limit-start-to-now') === true && dateObj.isBefore(moment()))
		{
			element.setCustomValidity("error");
		}
		else
		{
			element.setCustomValidity("");
		}

		var earlierThanLimit = Grocy.Components.DateTimePicker2.GetInputElement().data("earlier-than-limit");
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
	var shortcutValue = $("#datetimepicker2-shortcut").data("datetimepicker2-shortcut-value");
	if (value == shortcutValue && !$("#datetimepicker2-shortcut").is(":checked"))
	{
		$("#datetimepicker2-shortcut").click();
	}
});

Grocy.Components.DateTimePicker2.GetInputElement().on('input', function(e)
{
	$('#datetimepicker2-timeago').attr("datetime", Grocy.Components.DateTimePicker2.GetValue());
	EmptyElementWhenMatches('#datetimepicker2-timeago', __t('timeago_nan'));
	RefreshContextualTimeago("#datetimepicker2-wrapper");
});

$('.datetimepicker2').on('update.datetimepicker', function(e)
{
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('input');
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('change');
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('keypress');
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('keyup');
});

$('.datetimepicker2').on('hide.datetimepicker', function(e)
{
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('input');
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('change');
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('keypress');
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('keyup');
});

$("#datetimepicker2-shortcut").on("click", function()
{
	if (this.checked)
	{
		var value = $("#datetimepicker2-shortcut").data("datetimepicker2-shortcut-value");
		Grocy.Components.DateTimePicker2.SetValue(value);
		Grocy.Components.DateTimePicker2.GetInputElement().attr("readonly", "");
		$(Grocy.Components.DateTimePicker2.GetInputElement().data('next-input-selector')).focus();
	}
	else
	{
		Grocy.Components.DateTimePicker2.SetValue("");
		Grocy.Components.DateTimePicker2.GetInputElement().removeAttr("readonly");
		Grocy.Components.DateTimePicker2.GetInputElement().focus();
	}

	Grocy.Components.DateTimePicker2.GetInputElement().trigger('input');
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('change');
	Grocy.Components.DateTimePicker2.GetInputElement().trigger('keypress');
});
