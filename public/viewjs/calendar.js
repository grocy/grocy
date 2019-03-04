$("#calendar").fullCalendar({
	"themeSystem": "bootstrap4",
	"header": {
		"left": "month,basicWeek,listWeek",
		"center": "title",
		"right": "prev,next"
	},
	"weekNumbers": true,
	"eventLimit": true,
	"eventSources": fullcalendarEventSources
});

$("#ical-button").on("click", function(e)
{
	e.preventDefault();

	Grocy.Api.Get('calendar/ical/sharing-link',
		function(result)
		{
			location.href = result.url;
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
