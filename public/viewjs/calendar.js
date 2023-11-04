var firstDay = null;
if (Grocy.CalendarFirstDayOfWeek)
{
	firstDay = Number.parseInt(Grocy.CalendarFirstDayOfWeek);
}

var calendar = $("#calendar").fullCalendar({
	"themeSystem": "bootstrap4",
	"header": {
		"left": "month,agendaWeek,agendaDay,listWeek",
		"center": "title",
		"right": "prev,today,next"
	},
	"weekNumbers": Grocy.CalendarShowWeekNumbers,
	"defaultView": ($(window).width() < 768) ? "agendaDay" : "month",
	"firstDay": firstDay,
	"eventLimit": false,
	"height": "auto",
	"eventSources": fullcalendarEventSources,
	"eventClick": function(info)
	{
		location.href = info.link;
	},
	"timeFormat": "HH:mm"
});

$("#ical-button").on("click", function(e)
{
	e.preventDefault();

	Grocy.Api.Get('calendar/ical/sharing-link',
		function(result)
		{
			bootbox.alert({
				title: __t('Share/Integrate calendar (iCal)'),
				message: __t('Use the following (public) URL to share or integrate the calendar in iCal format') + '<input type="text" class="form-control form-control-sm mt-2 easy-link-copy-textbox" value="' + result.url + '"><p class="text-center mt-4">'
					+ QrCodeImgHtml(result.url) + "</p>",
				closeButton: false
			});
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(window).one("resize", function()
{
	// Automatically switch the calendar to "basicDay" view on small screens
	// and to "month" otherwise
	if ($(window).width() < 768)
	{
		calendar.fullCalendar("changeView", "agendaDay");
	}
	else
	{
		calendar.fullCalendar("changeView", "month");
	}
});

$("#configure-colors-button").on("click", function(e)
{
	e.preventDefault();

	$("#configure-colors-modal").modal("show");
});

$("#configure-colors-modal").on("hidden.bs.modal", function(e)
{
	window.location.href = U('/calendar');
})
