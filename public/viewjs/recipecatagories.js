var recipecatagoriesTable = $('#recipecatagories-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
});
$('#recipecatagories-table tbody').removeClass("d-none");
recipecatagoriesTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	recipecatagoriesTable.search(value).draw();
}, 200));

$(document).on('click', '.recipe-catagory-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-recipecatagory-name');
	var objectId = $(e.currentTarget).attr('data-recipecatagory-id');

	bootbox.confirm({
		message: __t('Are you sure to delete recipe catagory "%s"?', objectName),
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
				Grocy.Api.Delete('objects/recipe_catagories/' + objectId, {},
					function(result)
					{
						window.recipecatagory.href = U('/recipecatagories');
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
