var habitsOverviewTable = $('#habits-overview-table').DataTable({
	'paginate': false,
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	habitsOverviewTable.search(value).draw();
});

$(document).on('click', '.track-habit-button', function(e)
{
	var habitId = $(e.currentTarget).attr('data-habit-id');
	var habitName = $(e.currentTarget).attr('data-habit-name');
	var trackedTime = moment().format('YYYY-MM-DD HH:mm:ss');

	Grocy.Api.Get('habits/track-habit-execution/' + habitId + '?tracked_time=' + trackedTime,
		function(result)
		{
			$('#habit-' + habitId + '-last-tracked-time').parent().effect('highlight', {}, 500);
			$('#habit-' + habitId + '-last-tracked-time').fadeOut(500, function () {
				$(this).text(trackedTime).fadeIn(500);
			});
			$('#habit-' + habitId + '-last-tracked-time-timeago').attr('datetime', trackedTime);
			RefreshContextualTimeago();

			toastr.success(L('Tracked execution of habit #1 on #2', habitName, trackedTime));
			RefreshStatistics();
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

function RefreshStatistics()
{
	var nextXDays = $("#info-due-habits").data("next-x-days");
	Grocy.Api.Get('habits/get-current',
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
			
			$("#info-due-habits").text(Pluralize(dueCount, L('#1 habit is due to be done within the next #2 days', dueCount, nextXDays), L('#1 habits are due to be done within the next #2 days', dueCount, nextXDays)));
			$("#info-overdue-habits").text(Pluralize(overdueCount, L('#1 habit is overdue to be done', overdueCount), L('#1 habits are overdue to be done', overdueCount)));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

RefreshStatistics();
