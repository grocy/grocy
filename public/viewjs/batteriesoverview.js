var batteriesOverviewTable = $('#batteries-overview-table').DataTable({
	'paginate': false,
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(__t('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	}
});
$('#batteries-overview-table tbody').removeClass("d-none");
batteriesOverviewTable.columns.adjust().draw();

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

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var batteryId = $(e.currentTarget).attr('data-battery-id');
	var batteryName = $(e.currentTarget).attr('data-battery-name');
	var trackedTime = moment().format('YYYY-MM-DD HH:mm:ss');

	Grocy.Api.Post('batteries/' + batteryId + '/charge', { 'tracked_time': trackedTime },
		function()
		{
			Grocy.Api.Get('batteries/' + batteryId,
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

					Grocy.FrontendHelpers.EndUiBusy();
					toastr.success(__t('Tracked charge cycle of battery %1$s on %2$s', batteryName, trackedTime));
					RefreshContextualTimeago();
					RefreshStatistics();
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy();
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
			console.error(xhr);
		}
	);
});

$(document).on("click", ".battery-name-cell", function(e)
{
	Grocy.Components.BatteryCard.Refresh($(e.currentTarget).attr("data-battery-id"));
	$("#batteriesoverview-batterycard-modal").modal("show");
});

function RefreshStatistics()
{
	var nextXDays = $("#info-due-batteries").data("next-x-days");
	Grocy.Api.Get('batteries',
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

			$("#info-due-batteries").text(__n(dueCount, '%s battery is due to be charged', '%s batteries are due to be charged') + ' ' + __n(nextXDays, 'within the next day', 'within the next %s days'));
			$("#info-overdue-batteries").text(__n(overdueCount, '%s battery is overdue to be charged', '%s batteries are overdue to be charged'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

RefreshStatistics();
