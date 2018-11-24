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
