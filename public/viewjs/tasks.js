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

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	},
	'rowGroup': {
		dataSrc: 3
	}
});
$('#tasks-table tbody').removeClass("d-none");

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	tasksTable.search(value).draw();
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

	tasksTable.column(5).search(value).draw();
});

$(".status-filter-button").on("click", function()
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

	Grocy.Api.Get('tasks/' + taskId + '/complete?done_time=' + doneTime,
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

			Grocy.FrontendHelpers.EndUiBusy();
			toastr.success(L('Marked task #1 as completed on #2', taskName, doneTime));
			RefreshContextualTimeago();
			RefreshStatistics();
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
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
				Grocy.Api.Delete('object/tasks/' + objectId,
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
	Grocy.Api.Get('tasks',
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
