var batteriesOverviewTable = $('#batteries-overview-table').DataTable({
	'paginate': false,
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";
	}
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	batteriesOverviewTable.search(value).draw();
});

$("#status-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	// Transfer CSS classes of selected element to dropdown element (for background)
	$(this).attr("class", $("#" + $(this).attr("id") + " option[value='" + value + "']").attr("class") + " form-control");
	
	batteriesOverviewTable.column(4).search(value).draw();
});

$(".status-filter-button").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
});

$(document).on('click', '.track-charge-cycle-button', function(e)
{
	e.preventDefault();
	
	var batteryId = $(e.currentTarget).attr('data-battery-id');
	var batteryName = $(e.currentTarget).attr('data-battery-name');
	var trackedTime = moment().format('YYYY-MM-DD HH:mm:ss');

	Grocy.Api.Get('batteries/track-charge-cycle/' + batteryId + '?tracked_time=' + trackedTime,
		function()
		{
			Grocy.Api.Get('batteries/get-battery-details/' + batteryId,
				function(result)
				{
					var batteryRow = $('#battery-' + batteryId + '-row');
					var nextXDaysThreshold = moment().add($("#info-due-batteries").data("next-x-days"), "days");
					var now = moment();
					var nextExecutionTime = moment(result.next_estimated_charge_time);

					batteryRow.removeClass("table-warning");
					batteryRow.removeClass("table-danger");
					if (nextExecutionTime.isBefore(now))
					{
						batteryRow.addClass("table-danger");
					}
					else if (nextExecutionTime.isBefore(nextXDaysThreshold))
					{
						batteryRow.addClass("table-warning");
					}

					$('#battery-' + batteryId + '-last-tracked-time').parent().effect('highlight', { }, 500);
					$('#battery-' + batteryId + '-last-tracked-time').fadeOut(500, function()
					{
						$(this).text(trackedTime).fadeIn(500);
					});
					$('#battery-' + batteryId + '-last-tracked-time-timeago').attr('datetime', trackedTime);

					if (result.battery.charge_interval_days != 0)
					{
						$('#battery-' + batteryId + '-next-charge-time').parent().effect('highlight', { }, 500);
						$('#battery-' + batteryId + '-next-charge-time').fadeOut(500, function()
						{
							$(this).text(result.next_estimated_charge_time).fadeIn(500);
						});
						$('#battery-' + batteryId + '-next-charge-time-timeago').attr('datetime', result.next_estimated_charge_time);
					}

					toastr.success(L('Tracked charge cycle of battery #1 on #2', batteryName, trackedTime));
					RefreshContextualTimeago();
					RefreshStatistics();
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

function RefreshStatistics()
{
	var nextXDays = $("#info-due-batteries").data("next-x-days");
	Grocy.Api.Get('batteries/get-current',
		function(result)
		{
			var dueCount = 0;
			var overdueCount = 0;
			var now = moment();
			var nextXDaysThreshold = moment().add(nextXDays, "days");
			result.forEach(element => {
				var date = moment(element.next_estimated_charge_time);
				if (date.isBefore(now))
				{
					overdueCount++;
				}
				else if (date.isBefore(nextXDaysThreshold))
				{
					dueCount++;
				}
			});
			
			$("#info-due-batteries").text(Pluralize(dueCount, L('#1 battery is due to be charged within the next #2 days', dueCount, nextXDays), L('#1 batteries are due to be charged within the next #2 days', dueCount, nextXDays)));
			$("#info-overdue-batteries").text(Pluralize(overdueCount, L('#1 battery is overdue to be charged', overdueCount), L('#1 batteries are overdue to be charged', overdueCount)));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

RefreshStatistics();
