var tasksTable = $('#tasks-table').DataTable({
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
	
	tasksTable.search(value).draw();
});

$(document).on('click', '.do-task-button', function(e)
{
	var taskId = $(e.currentTarget).attr('data-task-id');
	var doneTime = moment().format('YYYY-MM-DD HH:mm:ss');

	Grocy.Api.Get('tasks/track-task-execution/' + taskId + '?tracked_time=' + trackedTime,
		function()
		{
			Grocy.Api.Get('tasks/get-task-details/' + taskId,
				function(result)
				{
					var taskRow = $('#task-' + taskId + '-row');
					var nextXDaysThreshold = moment().add($("#info-due-tasks").data("next-x-days"), "days");
					var now = moment();
					var nextExecutionTime = moment(result.next_estimated_execution_time);

					taskRow.removeClass("table-warning");
					taskRow.removeClass("table-danger");
					if (nextExecutionTime.isBefore(now))
					{
						taskRow.addClass("table-danger");
					}
					else if (nextExecutionTime.isBefore(nextXDaysThreshold))
					{
						taskRow.addClass("table-warning");
					}

					$('#task-' + taskId + '-last-tracked-time').parent().effect('highlight', { }, 500);
					$('#task-' + taskId + '-last-tracked-time').fadeOut(500, function()
					{
						$(this).text(trackedTime).fadeIn(500);
					});
					$('#task-' + taskId + '-last-tracked-time-timeago').attr('datetime', trackedTime);

					if (result.task.period_type == "dynamic-regular")
					{
						$('#task-' + taskId + '-next-execution-time').parent().effect('highlight', { }, 500);
						$('#task-' + taskId + '-next-execution-time').fadeOut(500, function()
						{
							$(this).text(result.next_estimated_execution_time).fadeIn(500);
						});
						$('#task-' + taskId + '-next-execution-time-timeago').attr('datetime', result.next_estimated_execution_time);
					}

					toastr.success(L('Tracked execution of task #1 on #2', taskName, trackedTime));
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
	var nextXDays = $("#info-due-tasks").data("next-x-days");
	Grocy.Api.Get('tasks/get-current',
		function(result)
		{
			var dueCount = 0;
			var overdueCount = 0;
			var now = moment();
			var nextXDaysThreshold = moment().add(nextXDays, "days");
			result.forEach(element => {
				var date = moment(element.due);
				if (date.isBefore(now))
				{
					overdueCount++;
				}
				else if (date.isBefore(nextXDaysThreshold))
				{
					dueCount++;
				}
			});
			
			$("#info-due-tasks").text(Pluralize(dueCount, L('#1 task is due to be done within the next #2 days', dueCount, nextXDays), L('#1 tasks are due to be done within the next #2 days', dueCount, nextXDays)));
			$("#info-overdue-tasks").text(Pluralize(overdueCount, L('#1 task is overdue to be done', overdueCount), L('#1 tasks are overdue to be done', overdueCount)));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

RefreshStatistics();
