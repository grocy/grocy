var batteriesJournalTable = $('#batteries-journal-table').DataTable({
	'paginate': true,
	'order': [[1, 'desc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(__t('datatables_localization')),
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
$('#batteries-journal-table tbody').removeClass("d-none");
batteriesJournalTable.columns.adjust().draw();

$("#battery-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#battery-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}

	batteriesJournalTable.column(1).search(text).draw();
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	batteriesJournalTable.search(value).draw();
});

if (typeof GetUriParam("battery") !== "undefined")
{
	$("#battery-filter").val(GetUriParam("battery"));
	$("#battery-filter").trigger("change");
}

$(document).on('click', '.undo-battery-execution-button', function(e)
{
	e.preventDefault();

	var element = $(e.currentTarget);
	var chargeCycleId = $(e.currentTarget).attr('data-charge-cycle-id');

	Grocy.Api.Post('batteries/charge-cycles/' + chargeCycleId.toString() + '/undo', { },
		function(result)
		{
			element.closest("tr").addClass("text-muted");
			element.parent().siblings().find("span.name-anchor").addClass("text-strike-through").after("<br>" + __t("Undone on") + " " + moment().format("YYYY-MM-DD HH:mm:ss") + " <time class='timeago timeago-contextual' datetime='" + moment().format("YYYY-MM-DD HH:mm:ss") + "'></time>");
			element.closest(".undo-stock-booking-button").addClass("disabled");
			RefreshContextualTimeago();
			toastr.success(__t("Charge cycle successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
