function choresjournalView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var choresJournalTable = $('#chores-journal-table').DataTable({
		'paginate': true,
		'order': [[2, 'desc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$('#chores-journal-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(choresJournalTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#chore-filter", 1, choresJournalTable);
	
	if (typeof GetUriParam("chore") !== "undefined")
	{
		$("#chore-filter").val(GetUriParam("chore"));
		$("#chore-filter").trigger("change");
	}
	
	$(document).on('click', '.undo-chore-execution-button', function(e)
	{
		e.preventDefault();
	
		var element = $(e.currentTarget);
		var executionId = element.attr('data-execution-id');
	
		Grocy.Api.Post('chores/executions/' + executionId.toString() + '/undo', {},
			function(result)
			{
				element.closest("tr").addClass("text-muted");
				element.parent().siblings().find("span.name-anchor").addClass("text-strike-through").after("<br>" + __t("Undone on") + " " + moment().format("YYYY-MM-DD HH:mm:ss") + " <time class='timeago timeago-contextual' datetime='" + moment().format("YYYY-MM-DD HH:mm:ss") + "'></time>");
				element.closest(".undo-stock-booking-button").addClass("disabled");
				RefreshContextualTimeago("#chore-execution-" + executionId + "-row");
				toastr.success(__t("Chore execution successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	});
	
}
