var tasksTable = $('#tasks-table').DataTable({
	'paginate': false,
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'visible': false, 'targets': 3 }
	],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";
	},
	'rowGroup': {
		dataSrc: 3
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
	e.preventDefault();
	
	var taskId = $(e.currentTarget).attr('data-task-id');
	var taskName = $(e.currentTarget).attr('data-task-name');
	var doneTime = moment().format('YYYY-MM-DD HH:mm:ss');

	Grocy.Api.Get('tasks/mark-task-as-completed/' + taskId + '?done_time=' + doneTime,
		function()
		{
			if (!$("#show-done-tasks").is(":checked"))
			{
				$('#task-' + taskId + '-row').fadeOut(500, function ()
				{
					$(this).remove();
				});
			}
			else
			{
				$('#task-' + taskId + '-row').addClass("text-muted");
				$('#task-' + taskId + '-name').addClass("text-strike-through");
				$('.do-task-button[data-task-id="' + taskId + '"]').addClass("disabled");
			}

			toastr.success(L('Marked task #1 as completed on #2', taskName, doneTime));
			RefreshContextualTimeago();
			RefreshStatistics();
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '.delete-task-button', function (e)
{
	e.preventDefault();

	var objectName = $(e.currentTarget).attr('data-task-name');
	var objectId = $(e.currentTarget).attr('data-task-id');

	bootbox.confirm({
		message: L('Are you sure to delete task "#1"?', objectName),
		buttons: {
			confirm: {
				label: L('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: L('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Get('delete-object/tasks/' + objectId,
					function(result)
					{
						$('#task-' + objectId + '-row').fadeOut(500, function ()
						{
							$(this).remove();
						});
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		}
	});
});

$("#show-done-tasks").change(function()
{
	if (this.checked)
	{
		window.location.href = U('/tasks?include_done');
	}
	else
	{
		window.location.href = U('/tasks');
	}
});

if (GetUriParam('include_done'))
{
	$("#show-done-tasks").prop('checked', true);
}

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
				var date = moment(element.due_date);
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
