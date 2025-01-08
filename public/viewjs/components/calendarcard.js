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
			time: 'fa-solid fa-clock',
			date: 'fa-solid fa-calendar',
			up: 'fa-solid fa-arrow-up',
			down: 'fa-solid fa-arrow-down',
			previous: 'fa-solid fa-chevron-left',
			next: 'fa-solid fa-chevron-right',
			today: 'fa-solid fa-calendar-check',
			clear: 'fa-solid fa-trash-can',
			close: 'fa-solid fa-circle-xmark'
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
