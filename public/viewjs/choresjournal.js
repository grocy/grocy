var choresJournalTable = $('#chores-journal-table').DataTable({
	'paginate': true,
	'order': [[1, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
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
	}
});

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

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	choresJournalTable.search(value).draw();
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

	Grocy.Api.Get('chores/undo-chore-execution/' + executionId.toString(),
		function(result)
		{
			element.closest("tr").addClass("text-muted");
			element.parent().siblings().find("span.name-anchor").addClass("text-strike-through").after("<br>" + L("Undone on") + " " + moment().format("YYYY-MM-DD HH:mm:ss") + " <time class='timeago timeago-contextual' datetime='" + moment().format("YYYY-MM-DD HH:mm:ss") + "'></time>");
			element.closest(".undo-stock-booking-button").addClass("disabled");
			RefreshContextualTimeago();
			toastr.success(L("Chore execution successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
