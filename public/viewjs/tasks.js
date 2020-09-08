var tasksTable = $('#tasks-table').DataTable({
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 3 }
	],
	'rowGroup': {
		dataSrc: 3
	}
});
$('#tasks-table tbody').removeClass("d-none");
tasksTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	tasksTable.search(value).draw();
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

	tasksTable.column(5).search(value).draw();
});

$(".status-filter-message").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
});

$(document).on('click', '.do-task-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var taskId = $(e.currentTarget).attr('data-task-id');
	var taskName = $(e.currentTarget).attr('data-task-name');
	var doneTime = moment().format('YYYY-MM-DD HH:mm:ss');

	Grocy.Api.Post('tasks/' + taskId + '/complete', { 'done_time': doneTime },
		function()
		{
			if (!$("#show-done-tasks").is(":checked"))
			{
				animateCSS("#task-" + taskId + "-row", "fadeOut", function()
				{
					$("#task-" + taskId + "-row").tooltip("hide");
					$("#task-" + taskId + "-row").remove();
				});
			}
			else
			{
				$('#task-' + taskId + '-row').addClass("text-muted");
				$('#task-' + taskId + '-name').addClass("text-strike-through");
				$('.do-task-button[data-task-id="' + taskId + '"]').addClass("disabled");
			}

			Grocy.FrontendHelpers.EndUiBusy();
			toastr.success(__t('Marked task %s as completed on %s', taskName, doneTime));
			RefreshContextualTimeago("#task-" + taskId + "-row");
			RefreshStatistics();
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
			console.error(xhr);
		}
	);
});

$(document).on('click', '.undo-task-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var taskId = $(e.currentTarget).attr('data-task-id');
	var taskName = $(e.currentTarget).attr('data-task-name');

	Grocy.Api.Post('tasks/' + taskId + '/undo', {},
		function()
		{
			window.location.reload();
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
			console.error(xhr);
		}
	);
});

$(document).on('click', '.delete-task-button', function(e)
{
	e.preventDefault();

	var objectName = SanitizeHtml($(e.currentTarget).attr('data-task-name'));
	var objectId = $(e.currentTarget).attr('data-task-id');

	bootbox.confirm({
		message: __t('Are you sure to delete task "%s"?', objectName),
		closeButton: false,
		buttons: {
			confirm: {
				label: __t('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: __t('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/tasks/' + objectId, {},
					function(result)
					{
						animateCSS("#task-" + objectId + "-row", "fadeOut", function()
						{
							$("#task-" + objectId + "-row").tooltip("hide");
							$("#task-" + objectId + "-row").remove();
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
	Grocy.Api.Get('tasks',
		function(result)
		{
			var dueCount = 0;
			var overdueCount = 0;
			var now = moment();
			var nextXDaysThreshold = moment().add(nextXDays, "days");
			result.forEach(element =>
			{
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

			$("#info-due-tasks").text(__n(dueCount, '%s task is due to be done', '%s tasks are due to be done') + ' ' + __n(nextXDays, 'within the next day', 'within the next %s days'));
			$("#info-overdue-tasks").text(__n(overdueCount, '%s task is overdue to be done', '%s tasks are overdue to be done'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

RefreshStatistics();
