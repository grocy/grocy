function batteriesjournalView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var batteriesJournalTable = $scope('#batteries-journal-table').DataTable({
		'paginate': true,
		'order': [[2, 'desc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#batteries-journal-table tbody').removeClass("d-none");
	Grocy.FrontendHelpers.InitDataTable(batteriesJournalTable);
	Grocy.FrontendHelpers.MakeFilterForColumn("#battery-filter", 1, batteriesJournalTable);

	if (typeof GetUriParam("battery") !== "undefined")
	{
		$scope("#battery-filter").val(GetUriParam("battery"));
		$scope("#battery-filter").trigger("change");
	}

	var top = scope != null ? $(scope) : $(document);

	top.on('click', '.undo-battery-execution-button', function(e)
	{
		e.preventDefault();

		var element = $(e.currentTarget);
		var chargeCycleId = $(e.currentTarget).attr('data-charge-cycle-id');

		Grocy.Api.Post('batteries/charge-cycles/' + chargeCycleId.toString() + '/undo', {},
			function(result)
			{
				element.closest("tr").addClass("text-muted");
				element.parent().siblings().find("span.name-anchor").addClass("text-strike-through").after("<br>" + __t("Undone on") + " " + moment().format("YYYY-MM-DD HH:mm:ss") + " <time class='timeago timeago-contextual' datetime='" + moment().format("YYYY-MM-DD HH:mm:ss") + "'></time>");
				element.closest(".undo-battery-execution-button").addClass("disabled");
				RefreshContextualTimeago("#charge-cycle-" + chargeCycleId + "-row");
				toastr.success(__t("Charge cycle successfully undone"));
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	});

}
