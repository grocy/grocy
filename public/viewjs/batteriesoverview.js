$('#batteries-overview-table').DataTable({
	'bPaginate': false,
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization'))
});

$(document).on('click', '.track-charge-cycle-button', function(e)
{
	var batteryId = $(e.currentTarget).attr('data-battery-id');
	var batteryName = $(e.currentTarget).attr('data-battery-name');
	var trackedTime = moment().format('YYYY-MM-DD HH:mm:ss');

	Grocy.Api.Get('batteries/track-charge-cycle/' + batteryId + '?tracked_time=' + trackedTime,
		function(result)
		{
			$('#battery-' + batteryId + '-last-tracked-time').parent().effect('highlight', {}, 500);
			$('#battery-' + batteryId + '-last-tracked-time').fadeOut(500, function () {
				$(this).text(trackedTime).fadeIn(500);
			});
			$('#battery-' + batteryId + '-last-tracked-time-timeago').attr('datetime', trackedTime);
			RefreshContextualTimeago();

			toastr.success(L('Tracked charge cylce of battery #1 on #2', batteryName, trackedTime));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
