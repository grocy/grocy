var firstDay = null;
if (!Grocy.CalendarFirstDayOfWeek.isEmpty())
{
	firstDay = parseInt(Grocy.CalendarFirstDayOfWeek);
}

$("#calendar").fullCalendar({
	"themeSystem": "bootstrap4",
	"header": {
		"left": "month,basicWeek,listWeek",
		"center": "title",
		"right": "prev,next"
	},
	"weekNumbers": true,
	"firstDay": firstDay,
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
				title: __t('Share/Integrate calendar (iCal)'),
				message: __t('Use the following (public) URL to share or integrate the calendar in iCal format') + '<input type="text" class="form-control form-control-sm mt-2 easy-link-copy-textbox" value="' + result.url + '">'
			});
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
