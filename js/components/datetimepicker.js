import { EmptyElementWhenMatches } from '../helpers/extensions'
import { RefreshContextualTimeago } from '../configs/timeago'

class datetimepicker
{
	constructor(Grocy, scopeSelector = null, basename = "datetimepicker")
	{
		this.Grocy = Grocy;

		this.scopeSelector = scopeSelector;
		this.scope = scopeSelector != null ? $(scopeSelector) : $(document);
		this.$ = scopeSelector != null ? (selector) => this.scope.find(selector) : $;

		this.basename = basename;

		var inputElement = this.GetInputElement();
		var self = this;


		this.startDate = null;
		if (inputElement.data('init-with-now') === true)
		{
			this.startDate = moment().format(inputElement.data('format'));
		}
		if (inputElement.data('init-value').length > 0)
		{
			this.startDate = moment(inputElement.data('init-value')).format(inputElement.data('format'));
		}

		this.limitDate = moment('2999-12-31 23:59:59');
		if (inputElement.data('limit-end-to-now') === true)
		{
			this.limitDate = moment();
		}

		// set some event handlers
		inputElement.on('keyup', (e) => self.keyupHandler(this, e));
		inputElement.on('input', (e) => self.inputHandler(this, e));

		this.$('.' + this.basename).on('update.datetimepicker', () => self.stateTrigger());
		this.$('.' + this.basename).on('hide.datetimepicker', () => self.stateTrigger());

		this.$("#" + this.basename + "-shortcut").on("click", () => self.handleShortcut(this));

		this.Init()
	}

	GetInputElement()
	{
		return this.$('.' + this.basename).find('input').not(".form-check-input");
	}

	GetValue()
	{
		return this.GetInputElement().val();
	}

	SetValue(value, triggerEvents = true)
	{
		// "Click" the shortcut checkbox when the desired value is
		// not the shortcut value and it is currently set
		var shortcutValue = this.$("#" + this.basename + "-shortcut").data(this.basename + "-shortcut-value");
		if (value != shortcutValue && this.$("#" + this.basename + "-shortcut").is(":checked"))
		{
			this.$("#" + this.basename + "-shortcut").click();
		}

		var inputElement = this.GetInputElement();
		inputElement.val(value);
		if (triggerEvents)
		{
			inputElement.trigger('change');

			inputElement.keyup();
		}
	}

	Clear()
	{
		this.$("." + this.basename).datetimepicker("destroy");
		this.Init();
		this.SetValue("", false);

		this.$('#' + this.basename + '-timeago').text('');
	}

	ChangeFormat(format)
	{
		this.$("." + this.basename).datetimepicker("destroy");
		var elem = this.GetInputElement();
		elem.data("format", format);
		this.Init();

		if (format == "YYYY-MM-DD")
		{
			elem.addClass("date-only-datetimepicker");
		}
		else
		{
			elem.removeClass("date-only-datetimepicker");
		}
	}

	Init()
	{
		this.$('.' + this.basename).datetimepicker(
			{
				format: this.GetInputElement().data('format'),
				buttons: {
					showToday: true,
					showClose: true
				},
				calendarWeeks: this.Grocy.CalendarShowWeekNumbers,
				maxDate: this.limitDate,
				locale: moment.locale(),
				defaultDate: this.startDate,
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


	keyupHandler(_this, e)
	{
		this.$('.' + this.basename).datetimepicker('hide');

		var inputElement = this.GetInputElement();

		var value = this.GetValue();
		var now = new Date();
		var centuryStart = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '00');
		var centuryEnd = Number.parseInt(now.getFullYear().toString().substring(0, 2) + '99');
		var format = inputElement.data('format');
		var nextInputElement = this.$(inputElement.data('next-input-selector'));

		//If input is empty and any arrow key is pressed, set date to today
		if (value.length === 0 && (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 37 || e.keyCode === 39))
		{
			this.SetValue(moment(new Date(), format, true).format(format));
			nextInputElement.focus();
		}
		else if (value === 'x' || value === 'X')
		{
			this.SetValue(moment('2999-12-31 23:59:59').format(format));
			nextInputElement.focus();
		}
		else if (value.length === 4 && !(Number.parseInt(value) > centuryStart && Number.parseInt(value) < centuryEnd))
		{
			var date = moment((new Date()).getFullYear().toString() + value);
			if (date.isBefore(moment()))
			{
				date.add(1, "year");
			}
			this.SetValue(date.format(format));
			nextInputElement.focus();
		}
		else if (value.length === 8 && $.isNumeric(value))
		{
			this.SetValue(value.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
			nextInputElement.focus();
		}
		else if (value.length === 7 && $.isNumeric(value.substring(0, 6)) && (value.substring(6, 7).toLowerCase() === "e" || value.substring(6, 7).toLowerCase() === "+"))
		{
			var endOfMonth = moment(value.substring(0, 4) + "-" + value.substring(4, 6) + "-01").endOf("month");
			this.SetValue(endOfMonth.format(format));
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
						this.SetValue(dateObj.add(-1, 'months').format(format));
					}
					else if (e.keyCode === 40) // Down
					{
						this.SetValue(dateObj.add(1, 'months').format(format));
					}
					else if (e.keyCode === 37) // Left
					{
						this.SetValue(dateObj.add(-1, 'years').format(format));
					}
					else if (e.keyCode === 39) // Right
					{
						this.SetValue(dateObj.add(1, 'years').format(format));
					}
				}
				else
				{
					// WITHOUT shift modifier key

					if (e.keyCode === 38) // Up
					{
						this.SetValue(dateObj.add(-1, 'days').format(format));
					}
					else if (e.keyCode === 40) // Down
					{
						this.SetValue(dateObj.add(1, 'days').format(format));
					}
					else if (e.keyCode === 37) // Left
					{
						this.SetValue(dateObj.add(-1, 'weeks').format(format));
					}
					else if (e.keyCode === 39) // Right
					{
						this.SetValue(dateObj.add(1, 'weeks').format(format));
					}
				}
			}
		}

		//Custom validation
		value = this.GetValue();
		dateObj = moment(value, format, true);
		var element = inputElement[0];
		if (!dateObj.isValid())
		{
			if (inputElement.hasAttr("required"))
			{
				element.setCustomValidity("error");
			}
		}
		else
		{
			if (inputElement.data('limit-end-to-now') === true && dateObj.isAfter(moment()))
			{
				element.setCustomValidity("error");
			}
			else if (inputElement.data('limit-start-to-now') === true && dateObj.isBefore(moment()))
			{
				element.setCustomValidity("error");
			}
			else
			{
				element.setCustomValidity("");
			}

			var earlierThanLimit = inputElement.data("earlier-than-limit");
			if (!earlierThanLimit.isEmpty())
			{
				if (moment(value).isBefore(moment(earlierThanLimit)))
				{
					this.$("#" + this.basename + "-earlier-than-info").removeClass("d-none");
				}
				else
				{
					this.$("#" + this.basename + "-earlier-than-info").addClass("d-none");
				}
			}
		}

		// "Click" the shortcut checkbox when the shortcut value was
		// entered manually and it is currently not set
		var shortcutValue = this.$("#" + this.basename + "-shortcut").data(this.basename + "-shortcut-value");
		if (value == shortcutValue && !this.$("#" + this.basename + "-shortcut").is(":checked"))
		{
			this.$("#" + this.basename + "-shortcut").click();
		}
	}

	inputHandler(_this, e)
	{
		this.$('#' + this.basename + '-timeago').attr("datetime", this.GetValue());
		EmptyElementWhenMatches(this.$('#' + this.basename + '-timeago'), this.Grocy.translate('timeago_nan'));

		// TODO: scoping
		RefreshContextualTimeago("#" + this.basename + "-wrapper");
	}

	stateTrigger()
	{
		var linputElement = this.GetInputElement();
		linputElement.trigger('input')
			.trigger('change')
			.trigger('keypress')
			.trigger('keyup');
	}

	handleShortcut(_this)
	{
		{
			var linputElement = this.GetInputElement();
			if (_this.checked)
			{
				var value = this.$("#" + this.basename + "-shortcut").data(this.basename + "-shortcut-value");
				this.SetValue(value);
				this.GetInputElement().attr("readonly", "");
				this.$(linputElement.data('next-input-selector')).focus();
			}
			else
			{
				this.SetValue("");
				linputElement.removeAttr("readonly");
				linputElement.focus();
			}

			this.stateTrigger();
		}
	}
}

export { datetimepicker }