/*
 * Metrics Javascript
 */

/* Charting */
var dataPoints = [];
$("#metrics-table tbody tr").each(function () {
	var self = $(this);
	var label = self.find("td:eq(0)").attr('data-chart-label');
	var value = Number(self.find("td:eq(1)").attr('data-chart-value'));
	var dataPoint = { label: label, y: parseFloat((Math.round(value * 100) / 100).toFixed(2))};
	dataPoints.push(dataPoint);
});

var options = {
	exportEnabled: true,
	legend:{
		horizontalAlign: "center",
		verticalAlign: "bottom"
	},
	data: [{
		type: "pie",
		showInLegend: true,
		toolTipContent: "<b>{label}</b>: ${y} (#percent%)",
		indexLabel: "{label}",
		legendText: "{label} (#percent%)",
		indexLabelPlacement: "outside",
		valueFormatSTringt: "#,##0.##",
		dataPoints: dataPoints
	}]
};

// needed for recursionCount error
recursionCount=0;
$("#metrics-chart").CanvasJSChart(options);

/* DataTables */
var metricsTable = $('#metrics-table').DataTable({
	"columnDefs": [
		{ "type": "num", "targets": 1 }
	]
});
$('#metrics-table tbody').removeClass("d-none");
metricsTable.columns.adjust().draw();

/* DateRangePicker */
const urlParams = new URLSearchParams(window.location.search);

var start_date = moment().startOf("month").format('MM/DD/YYYY');
var end_date = moment().endOf("month").format('MM/DD/YYYY');

if (urlParams.get('start_date')) start_date = moment(urlParams.get('start_date')) ;
if (urlParams.get('end_date')) end_date = moment(urlParams.get('end_date'));

$('#daterange-filter').daterangepicker({
	showDropdowns: true,
	startDate: start_date,
	endDate: end_date,
	locale: {
		"format": 'MM/DD/YYYY',
		"firstDay": 1
	},
	ranges: {
		'Today': [moment(), moment()],
		'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		'Last 7 Days': [moment().subtract(6, 'days'), moment()],
		'Last 14 Days': [moment().subtract(13, 'days'), moment()],
		'Last 30 Days': [moment().subtract(29, 'days'), moment()],
		'This Month': [moment().startOf('month'), moment().endOf('month')],
		'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
		'This Year': [moment().startOf('year'), moment().endOf('year')],
		'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
	}}, function(start, end, label) {
	UpdateUriParam("start_date", start.format('YYYY-MM-DD'));
	UpdateUriParam("end_date", end.format('YYYY-MM-DD'))
	window.location.reload();
});

$('#daterange-filter').on('cancel.daterangepicker', function(ev, picker)
{
	$(this).val(start_date + ' - ' + end_date);
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
