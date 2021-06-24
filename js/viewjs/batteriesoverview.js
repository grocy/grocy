function batteriesoverviewView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	var batterycard = Grocy.Use("batterycard");

	// preload some views.
	Grocy.PreloadView("batteriesjournal");
	Grocy.PreloadView("batteryform");


	var batteriesOverviewTable = $scope('#batteries-overview-table').DataTable({
		'order': [[4, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 },
			{ "type": "html", "targets": 3 },
			{ "type": "html", "targets": 4 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#batteries-overview-table tbody').removeClass("d-none");

	Grocy.FrontendHelpers.InitDataTable(batteriesOverviewTable);
	Grocy.FrontendHelpers.MakeStatusFilter(batteriesOverviewTable, 5);

	var top = scope != null ? $(scope) : $(document);

	top.on('click', '.track-charge-cycle-button', function(e)
	{
		e.preventDefault();

		// Remove the focus from the current button
		// to prevent that the tooltip stays until clicked anywhere else
		document.activeElement.blur();

		Grocy.FrontendHelpers.BeginUiBusy();

		var batteryId = $scope(e.currentTarget).attr('data-battery-id');
		var batteryName = $scope(e.currentTarget).attr('data-battery-name');
		var trackedTime = moment().format('YYYY-MM-DD HH:mm:ss');

		Grocy.Api.Post('batteries/' + batteryId + '/charge', { 'tracked_time': trackedTime },
			function()
			{
				Grocy.Api.Get('batteries/' + batteryId,
					function(result)
					{
						var batteryRow = $scope('#battery-' + batteryId + '-row');
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

						animateCSS("#battery-" + batteryId + "-row td:not(:first)", "shake");

						$scope('#battery-' + batteryId + '-last-tracked-time').text(trackedTime);
						$scope('#battery-' + batteryId + '-last-tracked-time-timeago').attr('datetime', trackedTime);
						if (result.battery.charge_interval_days != 0)
						{
							$scope('#battery-' + batteryId + '-next-charge-time').text(result.next_estimated_charge_time);
							$scope('#battery-' + batteryId + '-next-charge-time-timeago').attr('datetime', result.next_estimated_charge_time);
						}

						Grocy.FrontendHelpers.EndUiBusy();
						toastr.success(__t('Tracked charge cycle of battery %1$s on %2$s', batteryName, trackedTime));
						RefreshContextualTimeago("#battery-" + batteryId + "-row");
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



	top.on("click", ".battery-name-cell", function(e)
	{
		batterycard.Refresh($scope(e.currentTarget).attr("data-battery-id"));
		$scope("#batteriesoverview-batterycard-modal").modal("show");
	});

	function RefreshStatistics()
	{
		var nextXDays = $scope("#info-due-batteries").data("next-x-days");
		Grocy.Api.Get('batteries',
			function(result)
			{
				var dueCount = 0;
				var overdueCount = 0;
				var now = moment();
				var nextXDaysThreshold = moment().add(nextXDays, "days");
				result.forEach(element =>
				{
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

				$scope("#info-due-batteries").html('<span class="d-block d-md-none">' + dueCount + ' <i class="fas fa-clock"></i></span><span class="d-none d-md-block">' + __n(dueCount, '%s battery is due to be charged', '%s batteries are due to be charged') + ' ' + __n(nextXDays, 'within the next day', 'within the next %s days'));
				$scope("#info-overdue-batteries").html('<span class="d-block d-md-none">' + overdueCount + ' <i class="fas fa-times-circle"></i></span><span class="d-none d-md-block">' + __n(overdueCount, '%s battery is overdue to be charged', '%s batteries are overdue to be charged'));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}

	RefreshStatistics();

}


window.batteriesoverviewView = batteriesoverviewView
