var recipesTables = $('#recipes-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'orderData': 2, 'targets': 1 }
	],
	'select': 'single',
	'initComplete': function()
	{
		this.api().row({ order: 'current' }, 0).select();
	}
});
$('#recipes-table tbody').removeClass("d-none");
recipesTables.columns.adjust().draw();

if ((typeof GetUriParam("tab") !== "undefined" && GetUriParam("tab") === "gallery") || window.localStorage.getItem("recipes_last_tab_id") == "gallery-tab")
{
	$(".nav-tabs a[href='#gallery']").tab("show");
}

var recipe = GetUriParam("recipe");
if (typeof recipe !== "undefined")
{
	$("#recipes-table tr").removeClass("selected");
	var rowId = "#recipe-row-" + recipe;
	$(rowId).addClass("selected")

	var cardId = "#RecipeGalleryCard-" + recipe;
	$(cardId).addClass("border-primary");

	if ($(window).width() < 768)
	{
		// Scroll to recipe card on mobile
		$("#selectedRecipeCard")[0].scrollIntoView();
	}
}

if (GetUriParam("search") !== undefined)
{
	$("#search").val(GetUriParam("search"));
	setTimeout(function ()
	{
		$("#search").keyup();
	}, 50);
}

$("a[data-toggle='tab']").on("shown.bs.tab", function(e)
{
	var tabId = $(e.target).attr("id");
	window.localStorage.setItem("recipes_last_tab_id", tabId);
});

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();

	recipesTables.search(value).draw();

	$(".recipe-gallery-item").removeClass("d-none");
	console.log(	$(".recipe-gallery-item .card-title:not(:contains_case_insensitive(" + value + "))"));
	
	$(".recipe-gallery-item .card-title:not(:contains_case_insensitive(" + value + "))").parent().parent().parent().addClass("d-none");
}, 200));

$("#status-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	recipesTables.column(5).search(value).draw();
});

$(".recipe-delete").on('click', function(e)
{
	e.preventDefault();
	
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');

	bootbox.confirm({
		message: __t('Are you sure to delete recipe "%s"?', objectName),
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
				Grocy.Api.Delete('objects/recipes/' + objectId, {},
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

$(document).on('click', '.recipe-shopping-list', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');

	bootbox.confirm({
		message: __t('Are you sure to put all missing ingredients for recipe "%s" on the shopping list?', objectName) + "<br><br>" + __t("Uncheck ingredients to not put them on the shopping list") + ":" + $("#missing-recipe-pos-list")[0].outerHTML.replace("d-none", ""),
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
				Grocy.FrontendHelpers.BeginUiBusy();

				var excludedProductIds = new Array();
				$(".missing-recipe-pos-product-checkbox:checkbox:not(:checked)").each(function()
				{
					excludedProductIds.push($(this).data("product-id"));
				});

				Grocy.Api.Post('recipes/' + objectId + '/add-not-fulfilled-products-to-shoppinglist', { "excludedProductIds": excludedProductIds },
					function(result)
					{
						window.location.href = U('/recipes');
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy();
						console.error(xhr);
					}
				);
			}
		}
	});
});

$(".recipe-consume").on('click', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');

	bootbox.confirm({
		message: __t('Are you sure to consume all ingredients needed by recipe "%s" (ingredients marked with "check only if a single unit is in stock" will be ignored)?', objectName),
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
				Grocy.FrontendHelpers.BeginUiBusy();

				Grocy.Api.Post('recipes/' + objectId + '/consume', { },
					function(result)
					{
						Grocy.FrontendHelpers.EndUiBusy();
						toastr.success(__t('Removed all ingredients of recipe "%s" from stock', objectName));
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy();
						toastr.warning(__t('Not all ingredients of recipe "%s" are in stock, nothing removed', objectName));
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
		var currentRecipeId = location.search.split('recipe=')[1];
		if (selectedRecipeId.toString() !== currentRecipeId)
		{
			window.location.href = U('/recipes?recipe=' + selectedRecipeId.toString());
		}
	}
});

$(".recipe-gallery-item").on("click", function(e)
{
	e.preventDefault();

	window.location.href = U('/recipes?tab=gallery&recipe=' + $(this).data("recipe-id"));
});

$(".recipe-fullscreen").on('click', function(e)
{
	e.preventDefault();

	$("#selectedRecipeCard").toggleClass("fullscreen");
	$("body").toggleClass("fullscreen-card");
	$("#selectedRecipeCard .card-header").toggleClass("fixed-top");
	$("#selectedRecipeCard .card-body").toggleClass("mt-5");

	if ($("body").hasClass("fullscreen-card"))
	{
		window.location.hash = "#fullscreen";
	}
	else
	{
		window.history.replaceState(null, null, " ");
	}
});

$(".recipe-print").on('click', function(e)
{
	e.preventDefault();	

	$("#selectedRecipeCard").removeClass("fullscreen");
	$("body").removeClass("fullscreen-card");
	$("#selectedRecipeCard .card-header").removeClass("fixed-top");
	$("#selectedRecipeCard .card-body").removeClass("mt-5");

	window.history.replaceState(null, null, " ");
	window.print();
});

$('#servings-scale').keyup(function(event)
{
	var data = { };
	data.desired_servings = $(this).val();

	Grocy.Api.Put('objects/recipes/' + $(this).data("recipe-id"), data,
		function(result)
		{
			window.location.reload();
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on("click", ".missing-recipe-pos-select-button", function(e)
{
	e.preventDefault();

	var checkbox = $(this).find(".form-check-input");
	checkbox.prop("checked", !checkbox.prop("checked"));

	$(this).toggleClass("list-group-item-primary");
});

if (window.location.hash === "#fullscreen")
{
	$("#selectedRecipeToggleFullscreenButton").click();
}
