var collapsedGroups = {};

//tableId is desclared in shoppinglisttable.blade.php

var shoppingListTable = $(tableId).DataTable({
	'order': [[3, 'asc']],
	"orderFixed": [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 1 }
	].concat($.fn.dataTable.defaults.columnDefs),
	'rowGroup': {
		dataSrc: 1,
		startRender: function(rows, group)
		{
			var collapsed = !!collapsedGroups[group];
			var toggleClass = collapsed ? "fa-caret-right" : "fa-caret-down";

			rows.nodes().each(function(row)
			{
				row.style.display = collapsed ? "none" : "";
			});

			return $("<tr/>")
				.append('<td colspan="' + rows.columns()[0].length + '">' + group + ' <span class="fa fa-fw ' + toggleClass + '"/></td>')
				.attr("data-name", group)
				.toggleClass("collapsed", collapsed);
		}
	}
});

$(tableId + ' tbody').removeClass("d-none");
shoppingListTable.columns.adjust().draw();

$(document).on("click", "tr.dtrg-group", function()
{
	var name = $(this).data('name');
	collapsedGroups[name] = !collapsedGroups[name];
	shoppingListTable.draw();
});
