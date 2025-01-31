var recipesTables = $('#recipes-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 2 },
		{ "type": "html-num-fmt", "targets": 2 },
		{ "type": "html-num-fmt", "targets": 3 }
	].concat($.fn.dataTable.defaults.columnDefs),
	select: {
		style: 'single',
		selector: 'tr td:not(:first-child)'
	},
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
	setTimeout(function()
	{
		$("#search").keyup();
	}, 50);
}

if (GetUriParam("status") !== undefined)
{
	$("#status-filter").val(GetUriParam("status"));
	setTimeout(function()
	{
		$("#status-filter").trigger("change");
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

	if (!value)
	{
		RemoveUriParam("search");
	}
	else
	{
		UpdateUriParam("search", value);
	}

	$(".recipe-gallery-item").removeClass("d-none");
	$(".recipe-gallery-item .card-title-search:not(:contains_case_insensitive(" + value + "))").parent().parent().parent().addClass("d-none");
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	$("#status-filter").val("all");
	$("#search").trigger("keyup");
	$("#status-filter").trigger("change");
});

$("#status-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	recipesTables.column(recipesTables.colReorder.transpose(6)).search(value).draw();

	$('.recipe-gallery-item').removeClass('d-none');
	if (value !== "")
	{
		if (value === 'Xenoughinstock')
		{
			$('.recipe-gallery-item').not('.recipe-enoughinstock').addClass('d-none');
		}
		else if (value === 'enoughinstockwithshoppinglist')
		{
			$('.recipe-gallery-item').not('.recipe-enoughinstockwithshoppinglist').addClass('d-none');
		}
		if (value === 'notenoughinstock')
		{
			$('.recipe-gallery-item').not('.recipe-notenoughinstock').addClass('d-none');
		}
	}

	if (!value)
	{
		RemoveUriParam("status");
	}
	else
	{
		UpdateUriParam("status", value);
	}
});

$(".recipe-delete").on('click', function(e)
{
	e.preventDefault();

	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete recipe "%s"?', objectName),
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

$(".recipe-copy").on('click', function(e)
{
	e.preventDefault();

	var objectId = $(e.currentTarget).attr('data-recipe-id');

	Grocy.Api.Post("recipes/" + objectId.toString() + "/copy", {},
		function(result)
		{
			window.location.href = U('/recipes?recipe=' + result.created_object_id.toString());
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
		}
	);
});

$(document).on('click', '.recipe-shopping-list', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');

	bootbox.confirm({
		message: __t('Are you sure you want to put all missing ingredients for recipe "%s" on the shopping list?', objectName) + "<br><br>" + __t("Uncheck ingredients to not put them on the shopping list") + ":" + $("#missing-recipe-pos-list")[0].outerHTML.replace("d-none", ""),
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
						window.location.reload();
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
		message: __t('Are you sure you want to consume all ingredients needed by recipe "%s" (ingredients marked with "only check if any amount is in stock" will be ignored)?', objectName) +
			"<br><br>(" + __t("For ingredients that are only partially in stock, the in stock amount will be consumed.") + ")",
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

				Grocy.Api.Post('recipes/' + objectId + '/consume', {},
					function(result)
					{
						Grocy.FrontendHelpers.EndUiBusy();
						toastr.success(__t('Removed all in stock ingredients needed by recipe \"%s\" from stock', objectName));
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy();
						Grocy.FrontendHelpers.ShowGenericError("A server error occured while processing your request", xhr.response);
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

		if (BoolVal(Grocy.UserSettings.recipes_show_list_side_by_side))
		{
			if (selectedRecipeId.toString() !== currentRecipeId)
			{
				UpdateUriParam("recipe", selectedRecipeId.toString());
				window.location.reload();
			}
		}
		else
		{
			$("body").addClass("fullscreen-card");

			bootbox.dialog({
				message: '<iframe class="embed-responsive" src="' + U("/recipes?embedded&recipe=") + selectedRecipeId + '#fullscreen"></iframe>',
				size: 'extra-large',
				backdrop: true,
				closeButton: false,
				buttons: {
					cancel: {
						label: __t('Close'),
						className: 'btn-secondary responsive-button',
						callback: function()
						{
							$(".modal").last().modal("hide");
						}
					}
				}
			});
		}
	}
});

$(".recipe-gallery-item").on("click", function(e)
{
	e.preventDefault();

	var selectedRecipeId = $(this).data("recipe-id");

	if (BoolVal(Grocy.UserSettings.recipes_show_list_side_by_side))
	{
		window.location.href = U('/recipes?tab=gallery&recipe=' + selectedRecipeId);
	}
	else
	{
		$("body").addClass("fullscreen-card");

		bootbox.dialog({
			message: '<iframe class="embed-responsive" src="' + U("/recipes?embedded&recipe=") + selectedRecipeId + '#fullscreen"></iframe>',
			size: 'extra-large',
			backdrop: true,
			closeButton: false,
			buttons: {
				cancel: {
					label: __t('Close'),
					className: 'btn-secondary responsive-button',
					callback: function()
					{
						$(".modal").last().modal("hide");
					}
				}
			}
		});
	}
});

$(".recipe-edit-button").on("click", function(e)
{
	e.stopPropagation();
});

$(".recipe-fullscreen").on('click', function(e)
{
	e.preventDefault();

	$("#selectedRecipeCard").toggleClass("fullscreen");
	$("body").toggleClass("fullscreen-card");
	$("#selectedRecipeCard .card-header").toggleClass("fixed-top");
	$("#selectedRecipeCard .card-body").toggleClass("mt-5");
	$(".recipe-content-container").toggleClass("row");
	$(".recipe-content-container .ingredients").toggleClass("tab-pane").toggleClass("col-12 col-md-6 col-xl-4");
	$(".recipe-content-container .preparation").toggleClass("tab-pane").toggleClass("col-12 col-md-6 col-xl-8");
	$(".recipe-headline").toggleClass("d-none");

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
	var data = {};
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

$(document).on("click", ".missing-recipe-pos-product-checkbox", function(e)
{
	e.stopPropagation();

	$(this).prop("checked", !$(this).prop("checked"));
	$(this).parent().parent().click();
});

if (window.location.hash === "#fullscreen")
{
	$("#selectedRecipeToggleFullscreenButton").click();
}

$(document).on('click', '.recipe-grocycode-label-print', function(e)
{
	e.preventDefault();

	var recipeId = $(e.currentTarget).attr('data-recipe-id');
	Grocy.Api.Get('recipes/' + recipeId + '/printlabel', function(labelData)
	{
		if (Grocy.Webhooks.labelprinter !== undefined)
		{
			Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, labelData);
		}
	});
});

$(document).on('click', '.ingredient-done-button', function(e)
{
	e.preventDefault();

	$(e.currentTarget).parent().toggleClass("text-strike-through").toggleClass("text-muted");
});

$(document).on("click", ".add-to-mealplan-button", function(e)
{
	Grocy.Components.DateTimePicker.Init(true);
	Grocy.Components.DateTimePicker.SetValue(moment().format("YYYY-MM-DD"));
	Grocy.Components.RecipePicker.Clear();
	$("#add-to-mealplan-modal").modal("show");
	$('#recipe_id').val($(e.currentTarget).attr("data-recipe-id"));
	$('#recipe_id').data('combobox').refresh();
	$('#recipe_id').trigger('change');
	Grocy.FrontendHelpers.ValidateForm("add-to-mealplan-form");
	$("#recipe_servings").focus();
});

$('#save-add-to-mealplan-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("add-to-mealplan-form", true) || $(".combobox-menu-visible").length)
	{
		return false;
	}

	var formData = $('#add-to-mealplan-form').serializeJSON();
	formData.day = Grocy.Components.DateTimePicker.GetValue();

	Grocy.Api.Post('objects/meal_plan', formData,
		function(result)
		{
			toastr.success(__t("Successfully added the recipe to the meal plan"));
			$("#add-to-mealplan-modal").modal("hide");
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
		}
	);
});

$('#add-to-mealplan-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('add-to-mealplan-form'))
		{
			return false;
		}
		else
		{
			$("#save-add-to-mealplan-button").click();
		}
	}
});
