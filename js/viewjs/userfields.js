function userfieldsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var userfieldsTable = $scope('#userfields-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#userfields-table tbody').removeClass("d-none");

	Grocy.FrontendHelpers.InitDataTable(userfieldsTable, null, function()
	{
		$scope("#search").val("");
		$scope("#entity-filter").val("all");
		userfieldsTable.column(1).search("").draw();
		userfieldsTable.search("").draw();
	});

	$scope("#entity-filter").on("change", function()
	{
		var value = $scope("#entity-filter option:selected").text();
		if (value === __t("All"))
		{
			value = "";
		}

		userfieldsTable.column(1).search(value).draw();
		$scope("#new-userfield-button").attr("href", U("/userfield/new?embedded&entity=" + value));
	});

	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to delete user field "%s"?',
		'.userfield-delete-button',
		'data-userfield-name',
		'data-userfield-id',
		'objects/userfields/',
		'/userfields'
	);

	if (Grocy.GetUriParam("entity") != undefined && !Grocy.GetUriParam("entity").isEmpty())
	{
		$scope("#entity-filter").val(Grocy.GetUriParam("entity"));
		$scope("#entity-filter").trigger("change");
		$scope("#name").focus();
	}

}


window.userfieldsView = userfieldsView
