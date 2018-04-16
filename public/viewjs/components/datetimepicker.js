$(function()
{
	$('.datetimepicker').datetimepicker(
	{
		format: 'YYYY-MM-DD HH:mm:ss',
		showTodayButton: true,
		calendarWeeks: true,
		maxDate: moment(),
		locale: moment.locale('de')
	});
});
