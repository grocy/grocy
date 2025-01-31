var userfieldsTable = $('#userfields-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#userfields-table tbody').removeClass("d-none");
userfieldsTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	userfieldsTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#entity-filter").on("change", function()
{
	var value = $("#entity-filter option:selected").text();
	if (value === __t("All"))
	{
		value = "";
	}

	userfieldsTable.column(userfieldsTable.colReorder.transpose(1)).search(value).draw();
	$("#new-userfield-button").attr("href", U("/userfield/new?embedded&entity=" + value));
});

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#entity-filter").val("all");
	userfieldsTable.column(userfieldsTable.colReorder.transpose(1)).search("").draw();
	userfieldsTable.search("").draw();
});

$(document).on('click', '.userfield-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-userfield-name');
	var objectId = $(e.currentTarget).attr('data-userfield-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete user field "%s"?', objectName),
		closeButton: false,
		buttons: {
			confirm: {
				label: __t('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: __t('No'),
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

if (GetUriParam("entity"))
{
	$("#entity-filter").val(GetUriParam("entity"));
	$("#entity-filter").trigger("change");
	setTimeout(function()
	{
		$("#name").focus();
	}, Grocy.FormFocusDelay);
}
