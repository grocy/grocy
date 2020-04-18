var choresOverviewTable = $('#chores-overview-table').DataTable({
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
});
$('#chores-overview-table tbody').removeClass("d-none");
choresOverviewTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	choresOverviewTable.search(value).draw();
}, 200));

$("#status-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	// Transfer CSS classes of selected element to dropdown element (for background)
	$(this).attr("class", $("#" + $(this).attr("id") + " option[value='" + value + "']").attr("class") + " form-control");

	choresOverviewTable.column(5).search(value).draw();
});

$("#user-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	// Transfer CSS classes of selected element to dropdown element (for background)
	$(this).attr("class", $("#" + $(this).attr("id") + " option[value='" + value + "']").attr("class") + " form-control");

	choresOverviewTable.column(6).search(value).draw();

	if (!value.isEmpty())
	{
		UpdateUriParam("user", $("#user-filter option:selected").data("user-id"));
	}
});

$(".status-filter-message").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
});

$(".user-filter-message").on("click", function()
{
	var value = $(this).data("user-filter");
	$("#user-filter").val(value);
	$("#user-filter").trigger("change");
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
							$('#chore-' + choreId + '-due-filter-column').html("");
							if (nextExecutionTime.isBefore(now))
							{
								choreRow.addClass("table-danger");
								$('#chore-' + choreId + '-due-filter-column').html("overdue");
							}
							else if (nextExecutionTime.isBefore(nextXDaysThreshold))
							{
								choreRow.addClass("table-warning");
								$('#chore-' + choreId + '-due-filter-column').html("duesoon");
							}

							animateCSS("#chore-" + choreId + "-row td:not(:first)", "shake");

							$('#chore-' + choreId + '-last-tracked-time').text(trackedTime);
							$('#chore-' + choreId + '-last-tracked-time-timeago').attr('datetime', trackedTime);

							if (result.chore.period_type == "dynamic-regular")
							{
								$('#chore-' + choreId + '-next-execution-time').text(result.next_estimated_execution_time);
								$('#chore-' + choreId + '-next-execution-time-timeago').attr('datetime', result.next_estimated_execution_time);
							}

							if (result.chore.next_execution_assigned_to_user_id != null)
							{
								$('#chore-' + choreId + '-next-execution-assigned-user').text(result.next_execution_assigned_user.display_name);
							}

							Grocy.FrontendHelpers.EndUiBusy();
							toastr.success(__t('Tracked execution of chore %1$s on %2$s', choreName, trackedTime));
							RefreshStatistics();

							// Delay due to delayed/animated set of new timestamps above
							setTimeout(function()
							{
								RefreshContextualTimeago("#chore-" + choreId + "-row");

								// Refresh the DataTable to re-apply filters
								choresOverviewTable.rows().invalidate().draw(false);
								$(".input-group-filter").trigger("change");
							}, 550);
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
			var assignedToMeCount = 0;
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

				if (parseInt(element.next_execution_assigned_to_user_id) == Grocy.UserId)
				{
					assignedToMeCount++;
				}
			});

			$("#info-due-chores").text(__n(dueCount, '%s chore is due to be done', '%s chores are due to be done') + ' ' + __n(nextXDays, 'within the next day', 'within the next %s days'));
			$("#info-overdue-chores").text(__n(overdueCount, '%s chore is overdue to be done', '%s chores are overdue to be done'));
			$("#info-assigned-to-me-chores").text(__n(assignedToMeCount, '%s chore is assigned to me', '%s chores are assigned to me'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

if (GetUriParam("user") !== undefined)
{
	$("#user-filter").val("xx" + GetUriParam("user") + "xx");
	$("#user-filter").trigger("change");
}

RefreshStatistics();
