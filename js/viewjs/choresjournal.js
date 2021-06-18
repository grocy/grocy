﻿var choresJournalTable = $('#chores-journal-table').DataTable({
	'paginate': true,
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#chores-journal-table tbody').removeClass("d-none");
choresJournalTable.columns.adjust().draw();

$("#chore-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#chore-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	choresJournalTable.column(1).search(text).draw();
});

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	choresJournalTable.search(value).draw();
}, 200));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#chore-filter").val("all");
	choresJournalTable.column(1).search("").draw();
	choresJournalTable.search("").draw();
});

if (typeof GetUriParam("chore") !== "undefined")
{
	$("#chore-filter").val(GetUriParam("chore"));
	$("#chore-filter").trigger("change");
}

$(document).on('click', '.undo-chore-execution-button', function(e)
{
	e.preventDefault();

	var element = $(e.currentTarget);
	var executionId = $(e.currentTarget).attr('data-execution-id');

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
