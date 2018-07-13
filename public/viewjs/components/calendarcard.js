$('#calendar').datetimepicker(
{
	format: 'L',
	buttons: {
		showToday: true,
		showClose: false
	},
	calendarWeeks: true,
	locale: moment.locale(),
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
	keepOpen: true,
	inline: true,
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

$('#calendar').datetimepicker('show');
