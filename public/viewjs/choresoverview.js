var choresOverviewTable = $('#chores-overview-table').DataTable({
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
$('#chores-overview-table tbody').removeClass("d-none");
choresOverviewTable.columns.adjust().draw();

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	choresOverviewTable.search(value).draw();
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

	choresOverviewTable.column(4).search(value).draw();
});

$(".status-filter-button").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
});

$(document).on('click', '.track-chore-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var choreId = $(e.currentTarget).attr('data-chore-id');
	var choreName = $(e.currentTarget).attr('data-chore-name');

	Grocy.Api.Get('objects/chores/' + choreId,
		function(chore)
		{
			var trackedTime = moment().format('YYYY-MM-DD HH:mm:ss');
			if (chore.track_date_only == 1)
			{
				trackedTime = moment().format('YYYY-MM-DD');
			}

			Grocy.Api.Post('chores/' + choreId + '/execute', { 'tracked_time': trackedTime },
				function()
				{
					Grocy.Api.Get('chores/' + choreId,
						function(result)
						{
							var choreRow = $('#chore-' + choreId + '-row');
							var nextXDaysThreshold = moment().add($("#info-due-chores").data("next-x-days"), "days");
							var now = moment();
							var nextExecutionTime = moment(result.next_estimated_execution_time);

							choreRow.removeClass("table-warning");
							choreRow.removeClass("table-danger");
							if (nextExecutionTime.isBefore(now))
							{
								choreRow.addClass("table-danger");
							}
							else if (nextExecutionTime.isBefore(nextXDaysThreshold))
							{
								choreRow.addClass("table-warning");
							}

							$('#chore-' + choreId + '-last-tracked-time').parent().effect('highlight', { }, 500);
							$('#chore-' + choreId + '-last-tracked-time').fadeOut(500, function()
							{
								$(this).text(trackedTime).fadeIn(500);
							});
							$('#chore-' + choreId + '-last-tracked-time-timeago').attr('datetime', trackedTime);

							if (result.chore.period_type == "dynamic-regular")
							{
								$('#chore-' + choreId + '-next-execution-time').parent().effect('highlight', { }, 500);
								$('#chore-' + choreId + '-next-execution-time').fadeOut(500, function()
								{
									$(this).text(result.next_estimated_execution_time).fadeIn(500);
								});
								$('#chore-' + choreId + '-next-execution-time-timeago').attr('datetime', result.next_estimated_execution_time);
							}

							Grocy.FrontendHelpers.EndUiBusy();
							toastr.success(__t('Tracked execution of chore %1$s on %2$s', choreName, trackedTime));
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
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
			console.error(xhr);
		}
	);
});

$(document).on("click", ".chore-name-cell", function(e)
{
	Grocy.Components.ChoreCard.Refresh($(e.currentTarget).attr("data-chore-id"));
	$("#choresoverview-chorecard-modal").modal("show");
});

function RefreshStatistics()
{
	var nextXDays = $("#info-due-chores").data("next-x-days");
	Grocy.Api.Get('chores',
		function(result)
		{
			var dueCount = 0;
			var overdueCount = 0;
			var now = moment();
			var nextXDaysThreshold = moment().add(nextXDays, "days");
			result.forEach(element => {
				var date = moment(element.next_estimated_execution_time);
				if (date.isBefore(now))
				{
					overdueCount++;
				}
				else if (date.isBefore(nextXDaysThreshold))
				{
					dueCount++;
				}
			});

			$("#info-due-chores").text(__n(dueCount, '%s chore is due to be done', '%s chores are due to be done') + ' ' + __n(nextXDays, 'within the next day', 'within the next %s days'));
			$("#info-overdue-chores").text(__n(overdueCount, '%s chore is overdue to be done', '%s chores are overdue to be done'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

RefreshStatistics();
