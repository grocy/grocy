var firstRender = true;

var firstDay = null;
if (!Grocy.CalendarFirstDayOfWeek.isEmpty())
{
	firstDay = parseInt(Grocy.CalendarFirstDayOfWeek);
}

var calendar = $("#calendar").fullCalendar({
	"themeSystem": "bootstrap4",
	"header": {
		"left": "title",
		"center": "",
		"right": "prev,today,next"
	},
	"weekNumbers": false,
	"eventLimit": true,
	"eventSources": fullcalendarEventSources,
	"defaultView": "basicWeek",
	"firstDay": firstDay,
	"viewRender": function(view)
	{
		if (firstRender)
		{
			firstRender = false
		}
		else
		{
			UpdateUriParam("week", view.start.format("YYYY-MM-DD"));
		}
		
		$(".fc-day-header").append('<a class="ml-1 btn btn-outline-dark btn-xs my-1 add-recipe-button" href="#"><i class="fas fa-plus"></i></a>');

		var weekRecipeName = view.start.year().toString() + "-" + (view.start.week() - 1).toString();
		var weekRecipe = FindObjectInArrayByPropertyValue(internalRecipes, "name", weekRecipeName);
		
		var weekCosts = 0;
		var weekRecipeOrderMissingButtonHtml = "";
		var weekRecipeConsumeButtonHtml = "";
		if (weekRecipe !== null)
		{
			weekCosts = FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", weekRecipe.id).costs;

			var weekRecipeOrderMissingButtonDisabledClasses = "";
			if (FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", weekRecipe.id).need_fulfilled_with_shopping_list == 1)
			{
				weekRecipeOrderMissingButtonDisabledClasses = "disabled";
			}
			var weekRecipeConsumeButtonDisabledClasses = "";
			if (FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", weekRecipe.id).need_fulfilled == 0)
			{
				weekRecipeConsumeButtonDisabledClasses = "disabled";
			}
			weekRecipeOrderMissingButtonHtml = '<a class="ml-1 btn btn-outline-primary btn-xs recipe-order-missing-button ' + weekRecipeOrderMissingButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Put missing products on shopping list") + '" data-recipe-id="' + weekRecipe.id.toString() + '" data-recipe-name="' + weekRecipe.name + '" data-recipe-type="' + weekRecipe.type + '"><i class="fas fa-cart-plus"></i></a>'
			weekRecipeConsumeButtonHtml = '<a class="ml-1 btn btn-outline-success btn-xs recipe-consume-button ' + weekRecipeConsumeButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Consume all ingredients needed by this recipe") + '" data-recipe-id="' + weekRecipe.id.toString() + '" data-recipe-name="' + weekRecipe.name + '" data-recipe-type="' + weekRecipe.type + '"><i class="fas fa-utensils"></i></a>'
		}
		$(".fc-header-toolbar .fc-center").html("<h4>" + __t("Week costs") + ': <span class="locale-number-format" data-format="currency">' + weekCosts.toString() + "</span> " + weekRecipeOrderMissingButtonHtml + weekRecipeConsumeButtonHtml + "</h4>");
	},
	"eventRender": function(event, element)
	{
		var recipe = JSON.parse(event.recipe);
		var mealPlanEntry = JSON.parse(event.mealPlanEntry);
		var resolvedRecipe = FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", recipe.id);

		element.removeClass("fc-event");
		element.addClass("text-center");

		element.attr("data-recipe", event.recipe);
		element.attr("data-meal-plan-entry", event.mealPlanEntry);

		var recipeOrderMissingButtonDisabledClasses = "";
		if (resolvedRecipe.need_fulfilled_with_shopping_list == 1)
		{
			recipeOrderMissingButtonDisabledClasses = "disabled";
		}

		var recipeConsumeButtonDisabledClasses = "";
		if (resolvedRecipe.need_fulfilled == 0)
		{
			recipeConsumeButtonDisabledClasses = "disabled";
		}

		var fulfillmentInfoHtml = __t('Enough in stock');
		var fulfillmentIconHtml = '<i class="fas fa-check text-success"></i>';
		if (resolvedRecipe.need_fulfilled != 1)
		{
			fulfillmentInfoHtml = __t('Not enough in stock');
			var fulfillmentIconHtml = '<i class="fas fa-times text-danger"></i>';
		}

		element.html(' \
			<div class="text-truncate"> \
				<h5>' + recipe.name + '<h5> \
				<h5 class="small">' + __n(mealPlanEntry.servings, "%s serving", "%s servings") + '</h5> \
				<h5 class="small timeago-contextual">' + fulfillmentIconHtml + " " + fulfillmentInfoHtml + '</h5> \
				<h5 class="small"><span class="locale-number-format" data-format="currency">' + resolvedRecipe.costs + '</span> ' + __t('per serving') + '<h5> \
				<h5> \
					<a class="ml-1 btn btn-outline-danger btn-xs remove-recipe-button" href="#"><i class="fas fa-trash"></i></a> \
					<a class="ml-1 btn btn-outline-primary btn-xs recipe-order-missing-button ' + recipeOrderMissingButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Put missing products on shopping list") + '" data-recipe-id="' + recipe.id.toString() + '" data-recipe-name="' + recipe.name + '" data-recipe-type="' + recipe.type + '"><i class="fas fa-cart-plus"></i></a> \
					<a class="ml-1 btn btn-outline-success btn-xs recipe-consume-button ' + recipeConsumeButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Consume all ingredients needed by this recipe") + '" data-recipe-id="' + recipe.id.toString() + '" data-recipe-name="' + recipe.name + '" data-recipe-type="' + recipe.type + '"><i class="fas fa-utensils"></i></a> \
				</h5> \
			</div>');
		
		if (recipe.picture_file_name && !recipe.picture_file_name.isEmpty())
		{
			element.html(element.html() + '<img src="' + U("/api/files/recipepictures/") + btoa(recipe.picture_file_name) + '" class="img-fluid">')
		}
	},
	"eventAfterAllRender": function(view)
	{
		RefreshLocaleNumberDisplay();

		if (GetUriParam("week") !== undefined)
		{
			$("#calendar").fullCalendar("gotoDate", GetUriParam("week"));
		}
	},
});

$(document).on("click", ".add-recipe-button", function(e)
{
	var day = $(this).parent().data("date");

	$("#add-recipe-modal-title").text(__t("Add recipe to %s", day.toString()));
	$("#day").val(day.toString());
	Grocy.Components.RecipePicker.Clear();
	$("#add-recipe-modal").modal("show");
	Grocy.FrontendHelpers.ValidateForm("add-recipe-form");
});

$("#add-recipe-modal").on("shown.bs.modal", function(e)
{
	Grocy.Components.RecipePicker.GetInputElement().focus();
})

$(document).on("click", ".remove-recipe-button", function(e)
{
	var mealPlanEntry = JSON.parse($(this).parents(".fc-h-event:first").attr("data-meal-plan-entry"));

	Grocy.Api.Delete('objects/meal_plan/' + mealPlanEntry.id.toString(), { },
		function(result)
		{
			window.location.reload();
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
		}
	);
});

$('#save-add-recipe-button').on('click', function(e)
{
	e.preventDefault();

	if (document.getElementById("add-recipe-form").checkValidity() === false) //There is at least one validation error
	{
		return false;
	}

	Grocy.Api.Post('objects/meal_plan', $('#add-recipe-form').serializeJSON(),
		function(result)
		{
			window.location.reload();
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
		}
	);
});

Grocy.Components.RecipePicker.GetInputElement().keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById("add-recipe-form").checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$("#save-add-recipe-button").click();
		}
	}
});

$(document).on("keyodwn", "#servings", function(e)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById("add-recipe-form").checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$("#save-add-recipe-button").click();
		}
	}
});

$(document).on('click', '.recipe-order-missing-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');
	var button = $(this);

	bootbox.confirm({
		message: __t('Are you sure to put all missing ingredients for recipe "%s" on the shopping list?', objectName),
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

				Grocy.Api.Post('recipes/' + objectId + '/add-not-fulfilled-products-to-shoppinglist', { },
					function(result)
					{
						if (button.attr("data-recipe-type") == "normal")
						{
							button.addClass("disabled");
							Grocy.FrontendHelpers.EndUiBusy();
						}
						else
						{
							window.location.reload();
						}
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

$(document).on('click', '.recipe-consume-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');
	
	bootbox.confirm({
		message: __t('Are you sure to consume all ingredients needed by recipe "%s" (ingredients marked with "check only if a single unit is in stock" will be ignored)?', objectName),
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
						toastr.warning(__t('Not all ingredients of recipe "%s" are in stock, nothing removed', objectName));
						Grocy.FrontendHelpers.EndUiBusy();
						console.error(xhr);
					}
				);
			}
		}
	});
});
