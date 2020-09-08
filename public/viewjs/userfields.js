var userfieldsTable = $('#userfields-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
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
}, 200));

$("#entity-filter").on("change", function()
{
	var value = $("#entity-filter option:selected").text();
	if (value === __t("All"))
	{
		value = "";
	}

	userfieldsTable.column(1).search(value).draw();
	$("#new-userfield-button").attr("href", U("/userfield/new?entity=" + value));
});

$(document).on('click', '.userfield-delete-button', function(e)
{
	var objectName = SanitizeHtml($(e.currentTarget).attr('data-userfield-name'));
	var objectId = $(e.currentTarget).attr('data-userfield-id');

	bootbox.confirm({
		message: __t('Are you sure to delete user field "%s"?', objectName),
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

if (typeof GetUriParam("entity") !== "undefined" && !GetUriParam("entity").isEmpty())
{
	$("#entity-filter").val(GetUriParam("entity"));
	$("#entity-filter").trigger("change");
}
