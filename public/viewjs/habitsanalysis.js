var habitsAnalysisTable = $('#habits-analysis-table').DataTable({
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

$("#habit-filter").on("change", function()
{
	var value = $(this).val();
	var text = $("#habit-filter option:selected").text();
	if (value === "all")
	{
		text = "";
	}
	
	habitsAnalysisTable.column(0).search(text).draw();
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	habitsAnalysisTable.search(value).draw();
});

if (typeof GetUriParam("habit") !== "undefined")
{
	$("#habit-filter").val(GetUriParam("habit"));
	$("#habit-filter").trigger("change");
}
