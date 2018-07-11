$(function()
{
	$('.datetimepicker').datetimepicker(
	{
		format: 'YYYY-MM-DD HH:mm:ss',
		buttons: {
			showToday: true,
			showClose: true
		},
		calendarWeeks: true,
		maxDate: moment(),
		locale: moment.locale(),
		defaultDate: moment().format('YYYY-MM-DD HH:mm:ss'),
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
		sideBySide: true
	});
});
