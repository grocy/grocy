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
			bootbox.alert({
				title: L('Share/Integrate calendar (iCal)'),
				message: L('Use the following (public) URL to share or integrate the calendar in iCal format') + '<input type="text" class="form-control form-control-sm mt-2 easy-link-copy-textbox" value="' + result.url + '">'
			});
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
