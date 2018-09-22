var choresAnalysisTable = $('#chores-analysis-table').DataTable({
	'paginate': false,
	'order': [[1, 'desc']],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";
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
	
	choresAnalysisTable.column(0).search(text).draw();
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	choresAnalysisTable.search(value).draw();
});

if (typeof GetUriParam("chore") !== "undefined")
{
	$("#chore-filter").val(GetUriParam("chore"));
	$("#chore-filter").trigger("change");
}
