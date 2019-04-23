var userfieldsTable = $('#userfields-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
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
$('#userfields-table tbody').removeClass("d-none");
userfieldsTable.columns.adjust().draw();

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	userfieldsTable.search(value).draw();
});

$("#entity-filter").on("change", function()
{
	var value = $("#entity-filter option:selected").text();
	if (value === L("All"))
	{
		value = "";
	}

	userfieldsTable.column(1).search(value).draw();
	$("#new-userfield-button").attr("href", U("/userfield/new?entity=" + value));
});

$(document).on('click', '.userfield-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-userfield-name');
	var objectId = $(e.currentTarget).attr('data-userfield-id');

	bootbox.confirm({
		message: L('Are you sure to delete user field "#1"?', objectName),
		buttons: {
			confirm: {
				label: L('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: L('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/userfields/' + objectId, {},
					function(result)
					{
						window.location.href = U('/userfields');
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		}
	});
});

if (typeof GetUriParam("entity") !== "undefined" && !GetUriParam("entity").isEmpty())
{
	$("#entity-filter").val(GetUriParam("entity"));
	$("#entity-filter").trigger("change");
}
