var choresJournalTable = $('#chores-journal-table').DataTable({
	'order': [[2, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 4 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#chores-journal-table tbody').removeClass("d-none");
choresJournalTable.columns.adjust().draw();

$("#chore-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		RemoveUriParam("chore");
	}
	else
	{
		UpdateUriParam("chore", value);
	}

	window.location.reload();
});

$("#daterange-filter").on("change", function()
{
	UpdateUriParam("months", $(this).val());
	window.location.reload();
});

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	choresJournalTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#daterange-filter").val("24");
	RemoveUriParam("months");

	if (GetUriParam("embedded") === undefined)
	{
		$("#chore-filter").val("all");
		RemoveUriParam("chore");
	}

	window.location.reload();
});

if (typeof GetUriParam("chore") !== "undefined")
{
	$("#chore-filter").val(GetUriParam("chore"));
}

if (typeof GetUriParam("months") !== "undefined")
{
	$("#daterange-filter").val(GetUriParam("months"));
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
