var recipesTables = $('#recipes-table').DataTable({
	'paginate': false,
	'order': [[0, 'asc']],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'select': 'single',
	'initComplete': function()
	{
		this.api().row({ order: 'current' }, 0).select();
	}
});

var rowSelect = GetUriParam("row");
if (typeof rowSelect !== "undefined")
{
	recipesTables.row(rowSelect).select();
}

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	recipesTables.search(value).draw();
});

$("#selectedRecipeDeleteButton").on('click', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');

	bootbox.confirm({
		message: L('Are you sure to delete recipe "#1"?', objectName),
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
				Grocy.Api.Get('delete-object/recipes/' + objectId,
					function(result)
					{
						window.location.href = U('/recipes');
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

$(document).on('click', '.recipe-order-missing-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');

	bootbox.confirm({
		message: L('Are you sure to put all missing ingredients for recipe "#1" on the shopping list?', objectName),
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
				Grocy.Api.Get('recipes/add-not-fulfilled-products-to-shopping-list/' + objectId,
					function(result)
					{
						window.location.href = U('/recipes');
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

$("#selectedRecipeConsumeButton").on('click', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');

	bootbox.confirm({
		message: L('Are you sure to consume all ingredients needed by recipe "#1" (ingredients marked with "check only if a single unit is in stock" will be ignored)?', objectName),
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
				Grocy.Api.Get('recipes/consume-recipe/' + objectId,
					function(result)
					{
						toastr.success(L('Removed all ingredients of recipe "#1" from stock', objectName));
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

recipesTables.on('select', function(e, dt, type, indexes)
{
	if (type === 'row')
	{
		var selectedRecipeId = $(recipesTables.row(indexes[0]).node()).data("recipe-id");
		window.location.href = U('/recipes?recipe=' + selectedRecipeId.toString() + "&row=" + indexes[0].toString());
	}
});

$("#selectedRecipeToggleFullscreenButton").on('click', function(e)
{
	$("#selectedRecipeCard").toggleClass("fullscreen");
});
