/* Charting */
var labels = [];
var data = [];
var totalAmount = 0;
$("#metrics-table tbody tr").each(function()
{
	var self = $(this);
	labels.push(self.find("td:eq(0)").attr("data-chart-label"));
	var itemTotalRaw = Number.parseFloat(self.find("td:eq(1)").attr("data-chart-value"));
	var itemTotal = Number.parseFloat((Math.round(itemTotalRaw * 100) / 100).toFixed(2));
	data.push(itemTotal);
	totalAmount = (Number.parseFloat(totalAmount) + Number.parseFloat(itemTotal));
});
totalAmount = totalAmount.toLocaleString(undefined, { style: "currency", currency: Grocy.Currency });

var backgroundColorChoices = [
	"#6C747C",
	"#BFB8A4",
	"#BFADA4",
	"#4F575E",
	"#918B78",
	"#343A40",
	"#635E4F",
	"#63554F",
	"#1A1F24",
	"#383426",
	"#382C26",
	"#121B25",
	"#383119",
	"#382319"
]
var backgroundColors = [];
var colorChoiceIndex = 0;
for (i = 0; i < data.length; i++)
{
	if ((i + 1) == (backgroundColorChoices.length))
	{
		// restart background color choices
		colorChoiceIndex = 1;
	}
	backgroundColors.push(backgroundColorChoices[colorChoiceIndex]);
	colorChoiceIndex++;
}

var metricsChart = new Chart("metrics-chart", {
	"type": "outlabeledDoughnut",
	"options": {
		"legend": {
			"display": false
		},
		"tooltips": {
			"enabled": false
		},
		"tooltips": { enabled: false },
		"plugins": {
			"outlabels": {
				"text": "%l %p",
				"backgroundColor": "#343a40",
				"color": "white",
				"stretch": 45,
				"font": {
					"resizable": true,
					"minSize": 12,
					"maxSize": 18
				}
			},
			"doughnutlabel": {
				"labels": [
					{
						"text": totalAmount,
						"font": {
							"size": 24,
							"weight": "bold"
						},
					},
					{
						"text": __t("Total")
					}
				]
			}
		}
	},
	"data": {
		"labels": labels,
		"datasets": [{
			"data": data,
			"backgroundColor": backgroundColors
		}]
	}
});


/* DataTables */
var metricsTable = $("#metrics-table").DataTable({
	"columnDefs": [
		{ "type": "num", "targets": 1 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$("#metrics-table tbody").removeClass("d-none");
metricsTable.columns.adjust().draw();

/* DateRangePicker */
var startDate = moment().startOf("month").format("YYYY-MM-DD");
var endDate = moment().endOf("month").format("YYYY-MM-DD");

if (GetUriParam("start_date"))
{
	startDate = moment(GetUriParam("start_date"));
}
if (GetUriParam("end_date"))
{
	endDate = moment(GetUriParam("end_date"));
}

var ranges = {};
ranges[__t("Today")] = [moment(), moment()];
ranges[__t("Yesterday")] = [moment().subtract(1, "days"), moment().subtract(1, "days")];
ranges[__t("Last 7 Days")] = [moment().subtract(6, "days"), moment()];
ranges[__t("Last 14 Days")] = [moment().subtract(13, "days"), moment()];
ranges[__t("Last 30 Days")] = [moment().subtract(29, "days"), moment()];
ranges[__t("This Month")] = [moment().startOf("month"), moment().endOf("month")];
ranges[__t("Last Month")] = [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")];
ranges[__t("This Year")] = [moment().startOf("year"), moment().endOf("year")];
ranges[__t("Last Year")] = [moment().subtract(1, "year").startOf("year"), moment().subtract(1, "year").endOf("year")];

$("#daterange-filter").daterangepicker({
	"showDropdowns": true,
	"startDate": startDate,
	"endDate": endDate,
	"showWeekNumbers": Grocy.CalendarShowWeekNumbers,
	"locale": {
		"format": "YYYY-MM-DD",
		"firstDay": Grocy.CalendarFirstDayOfWeek
	},
	"applyLabel": __t("Apply"),
	"cancelLabel": __t("Cancel"),
	"customRangeLabel": __t("Custom Range"),
	"ranges": ranges
}, function(start, end, label)
{
	UpdateUriParam("start_date", start.format("YYYY-MM-DD"));
	UpdateUriParam("end_date", end.format("YYYY-MM-DD"))
	window.location.reload();
});

$("#daterange-filter").on("cancel.daterangepicker", function(ev, picker)
{
	$(this).val(start_date + " - " + end_date);
});

$("#clear-filter-button").on("click", function()
{
	RemoveUriParam("start_date");
	RemoveUriParam("end_date");
	RemoveUriParam("product_group");
	window.location.reload();
});

$("#product-group-filter").on("change", function()
{
	UpdateUriParam("product_group", $(this).val());
	window.location.reload();
});
