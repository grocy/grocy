/*
 * Metrics Javascript
 */

/* Charting */
var labels = [];
var data = [];
var totalAmount = 0;
$("#metrics-table tbody tr").each(function () {
	var self = $(this);
	labels.push(self.find("td:eq(0)").attr('data-chart-label'));
	var itemTotalRaw = parseFloat(self.find("td:eq(1)").attr('data-chart-value'));
	var itemTotal = parseFloat((Math.round(itemTotalRaw * 100) / 100).toFixed(2));
	data.push(itemTotal);
	totalAmount = (parseFloat(totalAmount) + parseFloat(itemTotal)).toFixed(2);
});

var backgroundColorChoices=['#6C747C',
							'#BFB8A4',
							'#BFADA4',
							'#4F575E',
							'#918B78',
							'#343A40',
							'#635E4F',
							'#63554F',
							'#1A1F24',
							'#383426',
							'#382C26',
							'#121B25',
							'#383119',
							'#382319']
var backgroundColors=[];
var colorChoiceIndex = 0;
for(let i=0;i<data.length;i++){
	if ((i + 1) == (backgroundColorChoices.length)){
		// restart background color choices
		colorChoiceIndex = 1;
	}
	backgroundColors.push(backgroundColorChoices[colorChoiceIndex]);
	colorChoiceIndex++;
}

var metricsChart = new Chart('metrics-chart', {
	type: 'outlabeledDoughnut',
	options: {
		legend: {
			display: false
		},
		tooltips: {
			enabled: false
		},
		tooltips: {enabled: false},
		plugins: {
			outlabels: {
				text: '%l %p',
				backgroundColor: "#343a40",
				color: 'white',
				stretch: 45,
				font: {
					resizable: true,
					minSize: 12,
					maxSize: 18
				}
			},
			doughnutlabel: {
				labels: [
					{
						text: totalAmount,
						font: {
							size: 30,
							weight: 'bold',
						},
					},
					{
						text: 'total',
					}
				]
			}
		}
	},
	data: {
		labels: labels,
		datasets: [{
			data: data,
			backgroundColor: backgroundColors
		}]
	}
});


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
