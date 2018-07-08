$('#habits-overview-table').DataTable({
	'bPaginate': false,
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
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
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
