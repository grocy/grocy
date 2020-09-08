var firstRender = true;
Grocy.IsMealPlanEntryEditAction = false;
Grocy.MealPlanEntryEditObjectId = -1;

var firstDay = null;
if (!Grocy.CalendarFirstDayOfWeek.isEmpty())
{
	firstDay = parseInt(Grocy.CalendarFirstDayOfWeek);
}
if (!Grocy.MealPlanFirstDayOfWeek.isEmpty())
{
	firstDay = parseInt(Grocy.MealPlanFirstDayOfWeek);
}

var calendar = $("#calendar").fullCalendar({
	"themeSystem": "bootstrap4",
	"header": {
		"left": "title",
		"center": "",
		"right": "prev,today,next"
	},
	"weekNumbers": false,
	"eventLimit": false,
	"eventSources": fullcalendarEventSources,
	"defaultView": ($(window).width() < 768) ? "basicDay" : "basicWeek",
	"firstDay": firstDay,
	"height": "auto",
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

		$(".fc-day-header").prepend('\
			<div class="btn-group mr-2 my-1"> \
				<button type="button" class="btn btn-outline-dark btn-xs add-recipe-button""><i class="fas fa-plus"></i></a></button> \
				<button type="button" class="btn btn-outline-dark btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button> \
				<div class="dropdown-menu"> \
					<a class="dropdown-item add-note-button" href="#">' + __t('Add note') + '</a> \
					<a class="dropdown-item add-product-button" href="#">' + __t('Add product') + '</a> \
				</div> \
			</div>');

		var weekRecipeName = view.start.year().toString() + "-" + ((view.start.week() - 1).toString().padStart(2, "0")).toString();
		var weekRecipe = FindObjectInArrayByPropertyValue(internalRecipes, "name", weekRecipeName);

		var weekCosts = 0;
		var weekRecipeOrderMissingButtonHtml = "";
		var weekRecipeConsumeButtonHtml = "";
		var weekCostsHtml = "";
		if (weekRecipe !== null)
		{
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				weekCosts = FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", weekRecipe.id).costs;
				weekCostsHtml = __t("Week costs") + ': <span class="locale-number locale-number-currency">' + weekCosts.toString() + "</span> ";
			}

			var weekRecipeOrderMissingButtonDisabledClasses = "";
			if (FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", weekRecipe.id).need_fulfilled_with_shopping_list == 1)
			{
				weekRecipeOrderMissingButtonDisabledClasses = "disabled";
			}

			var weekRecipeConsumeButtonDisabledClasses = "";
			if (FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", weekRecipe.id).need_fulfilled == 0 || weekCosts == 0)
			{
				weekRecipeConsumeButtonDisabledClasses = "disabled";
			}
			weekRecipeOrderMissingButtonHtml = '<a class="ml-1 btn btn-outline-primary btn-xs recipe-order-missing-button ' + weekRecipeOrderMissingButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Put missing products on shopping list") + '" data-recipe-id="' + weekRecipe.id.toString() + '" data-recipe-name="' + weekRecipe.name + '" data-recipe-type="' + weekRecipe.type + '"><i class="fas fa-cart-plus"></i></a>'
			weekRecipeConsumeButtonHtml = '<a class="ml-1 btn btn-outline-success btn-xs recipe-consume-button ' + weekRecipeConsumeButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Consume all ingredients needed by this weeks recipes or products") + '" data-recipe-id="' + weekRecipe.id.toString() + '" data-recipe-name="' + weekRecipe.name + '" data-recipe-type="' + weekRecipe.type + '"><i class="fas fa-utensils"></i></a>'
		}
		$(".fc-header-toolbar .fc-center").html("<h4>" + weekCostsHtml + weekRecipeOrderMissingButtonHtml + weekRecipeConsumeButtonHtml + "</h4>");
	},
	"eventRender": function(event, element)
	{
		element.removeClass("fc-event");
		element.addClass("text-center");
		element.attr("data-meal-plan-entry", event.mealPlanEntry);

		var mealPlanEntry = JSON.parse(event.mealPlanEntry);

		if (event.type == "recipe")
		{
			var recipe = JSON.parse(event.recipe);
			if (recipe === null || recipe === undefined)
			{
				return false;
			}

			var resolvedRecipe = FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", recipe.id);

			element.attr("data-recipe", event.recipe);

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
			var costsAndCaloriesPerServing = ""
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				costsAndCaloriesPerServing = '<h5 class="small text-truncate"><span class="locale-number locale-number-currency">' + resolvedRecipe.costs + '</span> / <span class="locale-number locale-number-generic">' + resolvedRecipe.calories + '</span> kcal ' + __t('per serving') + '<h5>';
			}
			else
			{
				costsAndCaloriesPerServing = '<h5 class="small text-truncate"><span class="locale-number locale-number-generic">' + resolvedRecipe.calories + '</span> kcal ' + __t('per serving') + '<h5>';
			}

			element.html('\
				<div> \
					<h5 class="text-truncate">' + recipe.name + '<h5> \
					<h5 class="small text-truncate">' + __n(mealPlanEntry.recipe_servings, "%s serving", "%s servings") + '</h5> \
					<h5 class="small timeago-contextual text-truncate">' + fulfillmentIconHtml + " " + fulfillmentInfoHtml + '</h5> \
					' + costsAndCaloriesPerServing + ' \
					<h5> \
						<a class="ml-1 btn btn-outline-danger btn-xs remove-recipe-button" href="#"><i class="fas fa-trash"></i></a> \
						<a class="ml-1 btn btn-outline-info btn-xs edit-meal-plan-entry-button" href="#"><i class="fas fa-edit"></i></a> \
						<a class="ml-1 btn btn-outline-primary btn-xs recipe-order-missing-button ' + recipeOrderMissingButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Put missing products on shopping list") + '" data-recipe-id="' + recipe.id.toString() + '" data-mealplan-servings="' + mealPlanEntry.recipe_servings + '" data-recipe-name="' + recipe.name + '" data-recipe-type="' + recipe.type + '"><i class="fas fa-cart-plus"></i></a> \
						<a class="ml-1 btn btn-outline-success btn-xs recipe-consume-button ' + recipeConsumeButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Consume all ingredients needed by this recipe") + '" data-recipe-id="' + recipe.id.toString() + '" data-mealplan-servings="' + mealPlanEntry.recipe_servings + '" data-recipe-name="' + recipe.name + '" data-recipe-type="' + recipe.type + '"><i class="fas fa-utensils"></i></a> \
						<a class="ml-1 btn btn-outline-secondary btn-xs recipe-popup-button" href="#" data-toggle="tooltip" title="' + __t("Display recipe") + '" data-recipe-id="' + recipe.id.toString() + '" data-recipe-name="' + recipe.name + '" data-mealplan-servings="' + mealPlanEntry.recipe_servings + '" data-recipe-type="' + recipe.type + '"><i class="fas fa-eye"></i></a> \
					</h5> \
				</div>');

			if (recipe.picture_file_name && !recipe.picture_file_name.isEmpty())
			{
				element.html(element.html() + '<div class="mx-auto"><img data-src="' + U("/api/files/recipepictures/") + btoa(recipe.picture_file_name) + '?force_serve_as=picture&best_fit_width=400" class="img-fluid lazy"></div>')
			}

			var dayRecipeName = event.start.format("YYYY-MM-DD");
			if (!$("#day-summary-" + dayRecipeName).length) // This runs for every event/recipe, so maybe multiple times per day, so only add the day summary once
			{
				var dayRecipe = FindObjectInArrayByPropertyValue(internalRecipes, "name", dayRecipeName);
				if (dayRecipe != null)
				{
					var dayRecipeResolved = FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", dayRecipe.id);

					var costsAndCaloriesPerDay = ""
					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					{
						costsAndCaloriesPerDay = '<h5 class="small text-truncate"><span class="locale-number locale-number-currency">' + dayRecipeResolved.costs + '</span> / <span class="locale-number locale-number-generic">' + dayRecipeResolved.calories + '</span> kcal ' + __t('per day') + '<h5>';
					}
					else
					{
						costsAndCaloriesPerDay = '<h5 class="small text-truncate"><span class="locale-number locale-number-generic">' + dayRecipeResolved.calories + '</span> kcal ' + __t('per day') + '<h5>';
					}
					$(".fc-day-header[data-date='" + dayRecipeName + "']").append('<h5 id="day-summary-' + dayRecipeName + '" class="small text-truncate border-top pt-1 pb-0">' + costsAndCaloriesPerDay + '</h5>');
				}
			}
		}
		if (event.type == "product")
		{
			var productDetails = JSON.parse(event.productDetails);
			if (productDetails === null || productDetails === undefined)
			{
				return false;
			}

			if (productDetails.last_price === null)
			{
				productDetails.last_price = 0;
			}

			if (productDetails.last_qu_factor_purchase_to_stock === null)
			{
				productDetails.last_qu_factor_purchase_to_stock = 1;
			}

			element.attr("data-product-details", event.productDetails);

			var productOrderMissingButtonDisabledClasses = "disabled";
			if (parseFloat(productDetails.stock_amount_aggregated) < parseFloat(mealPlanEntry.product_amount))
			{
				productOrderMissingButtonDisabledClasses = "";
			}

			var productConsumeButtonDisabledClasses = "disabled";
			if (parseFloat(productDetails.stock_amount_aggregated) >= parseFloat(mealPlanEntry.product_amount))
			{
				productConsumeButtonDisabledClasses = "";
			}

			fulfillmentInfoHtml = __t('Not enough in stock');
			var fulfillmentIconHtml = '<i class="fas fa-times text-danger"></i>';
			if (parseFloat(productDetails.stock_amount_aggregated) >= parseFloat(mealPlanEntry.product_amount))
			{
				var fulfillmentInfoHtml = __t('Enough in stock');
				var fulfillmentIconHtml = '<i class="fas fa-check text-success"></i>';
			}

			var costsAndCaloriesPerServing = ""
			if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
			{
				costsAndCaloriesPerServing = '<h5 class="small text-truncate"><span class="locale-number locale-number-currency">' + productDetails.last_price / productDetails.last_qu_factor_purchase_to_stock * mealPlanEntry.product_amount + '</span> / <span class="locale-number locale-number-generic">' + productDetails.product.calories * mealPlanEntry.product_amount + '</span> kcal ' + '<h5>';
			}
			else
			{
				costsAndCaloriesPerServing = '<h5 class="small text-truncate"><span class="locale-number locale-number-generic">' + productDetails.product.calories * mealPlanEntry.product_amount + '</span> kcal ' + '<h5>';
			}

			element.html('\
				<div> \
					<h5 class="text-truncate">' + productDetails.product.name + '<h5> \
					<h5 class="small text-truncate"><span class="locale-number locale-number-quantity-amount">' + mealPlanEntry.product_amount + "</span> " + __n(mealPlanEntry.product_amount, productDetails.quantity_unit_purchase.name, productDetails.quantity_unit_purchase.name_plural) + '</h5> \
					<h5 class="small timeago-contextual text-truncate">' + fulfillmentIconHtml + " " + fulfillmentInfoHtml + '</h5> \
					' + costsAndCaloriesPerServing + ' \
					<h5> \
						<a class="ml-1 btn btn-outline-danger btn-xs remove-product-button" href="#"><i class="fas fa-trash"></i></a> \
						<a class="ml-1 btn btn-outline-info btn-xs edit-meal-plan-entry-button" href="#"><i class="fas fa-edit"></i></a> \
						<a class="ml-1 btn btn-outline-success btn-xs product-consume-button ' + productConsumeButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Consume %1$s of %2$s", parseFloat(mealPlanEntry.product_amount).toLocaleString() + ' ' + __n(mealPlanEntry.product_amount, productDetails.quantity_unit_purchase.name, productDetails.quantity_unit_purchase.name_plural), productDetails.product.name) + '" data-product-id="' + productDetails.product.id.toString() + '" data-product-name="' + productDetails.product.name + '" data-product-amount="' + mealPlanEntry.product_amount + '"><i class="fas fa-utensils"></i></a> \
						<a class="ml-1 btn btn-outline-primary btn-xs show-as-dialog-link ' + productOrderMissingButtonDisabledClasses + '" href="' + U("/shoppinglistitem/new?embedded&updateexistingproduct&product=") + mealPlanEntry.product_id + '&amount=' + mealPlanEntry.product_amount + '" data-toggle="tooltip" title="' + __t("Add to shopping list") + '" data-product-id="' + productDetails.product.id.toString() + '" data-product-name="' + productDetails.product.name + '" data-product-amount="' + mealPlanEntry.product_amount + '"><i class="fas fa-cart-plus"></i></a> \
					</h5> \
				</div>');

			if (productDetails.product.picture_file_name && !productDetails.product.picture_file_name.isEmpty())
			{
				element.html(element.html() + '<div class="mx-auto"><img data-src="' + U("/api/files/productpictures/") + btoa(productDetails.product.picture_file_name) + '?force_serve_as=picture&best_fit_width=400" class="img-fluid lazy"></div>')
			}

			var dayRecipeName = event.start.format("YYYY-MM-DD");
			if (!$("#day-summary-" + dayRecipeName).length) // This runs for every event/recipe, so maybe multiple times per day, so only add the day summary once
			{
				var dayRecipe = FindObjectInArrayByPropertyValue(internalRecipes, "name", dayRecipeName);
				if (dayRecipe != null)
				{
					var dayRecipeResolved = FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", dayRecipe.id);

					var costsAndCaloriesPerDay = ""
					if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
					{
						costsAndCaloriesPerDay = '<h5 class="small text-truncate"><span class="locale-number locale-number-currency">' + dayRecipeResolved.costs + '</span> / <span class="locale-number locale-number-generic">' + dayRecipeResolved.calories + '</span> kcal ' + __t('per day') + '<h5>';
					}
					else
					{
						costsAndCaloriesPerDay = '<h5 class="small text-truncate"><span class="locale-number locale-number-generic">' + dayRecipeResolved.calories + '</span> kcal ' + __t('per day') + '<h5>';
					}
					$(".fc-day-header[data-date='" + dayRecipeName + "']").append('<h5 id="day-summary-' + dayRecipeName + '" class="small text-truncate border-top pt-1 pb-0">' + costsAndCaloriesPerDay + '</h5>');
				}
			}
		}
		else if (event.type == "note")
		{
			element.html('\
				<div> \
					<h5 class="text-wrap text-break">' + mealPlanEntry.note + '<h5> \
					<h5> \
						<a class="ml-1 btn btn-outline-danger btn-xs remove-note-button" href="#"><i class="fas fa-trash"></i></a> \
						<a class="ml-1 btn btn-outline-info btn-xs edit-meal-plan-entry-button" href="#"><i class="fas fa-edit"></i></a> \
					</h5> \
				</div>');
		}
	},
	"eventAfterAllRender": function(view)
	{
		RefreshLocaleNumberDisplay();
		LoadImagesLazy();
		$('[data-toggle="tooltip"]').tooltip();

		if (GetUriParam("week") !== undefined)
		{
			$("#calendar").fullCalendar("gotoDate", GetUriParam("week"));
		}
	},
});

$(document).on("click", ".add-recipe-button", function(e)
{
	var day = $(this).parent().parent().data("date");

	$("#add-recipe-modal-title").text(__t("Add recipe on %s", day.toString()));
	$("#day").val(day.toString());
	Grocy.Components.RecipePicker.Clear();
	$("#add-recipe-modal").modal("show");
	Grocy.FrontendHelpers.ValidateForm("add-recipe-form");
	Grocy.IsMealPlanEntryEditAction = false;
});

$(document).on("click", ".add-note-button", function(e)
{
	var day = $(this).parent().parent().parent().data("date");

	$("#add-note-modal-title").text(__t("Add note on %s", day.toString()));
	$("#day").val(day.toString());
	$("#note").val("");
	$("#add-note-modal").modal("show");
	Grocy.FrontendHelpers.ValidateForm("add-note-form");
	Grocy.IsMealPlanEntryEditAction = false;
});

$(document).on("click", ".add-product-button", function(e)
{
	var day = $(this).parent().parent().parent().data("date");

	$("#add-product-modal-title").text(__t("Add product on %s", day.toString()));
	$("#day").val(day.toString());
	Grocy.Components.ProductPicker.Clear();
	$("#add-product-modal").modal("show");
	Grocy.FrontendHelpers.ValidateForm("add-product-form");
	Grocy.IsMealPlanEntryEditAction = false;
});

$(document).on("click", ".edit-meal-plan-entry-button", function(e)
{
	var mealPlanEntry = JSON.parse($(this).parents(".fc-h-event:first").attr("data-meal-plan-entry"));

	if (mealPlanEntry.type == "recipe")
	{
		$("#add-recipe-modal-title").text(__t("Edit recipe on %s", mealPlanEntry.day.toString()));
		$("#day").val(mealPlanEntry.day.toString());
		$("#recipe_servings").val(mealPlanEntry.recipe_servings);
		Grocy.Components.RecipePicker.SetId(mealPlanEntry.recipe_id);
		$("#add-recipe-modal").modal("show");
		Grocy.FrontendHelpers.ValidateForm("add-recipe-form");
	}
	else if (mealPlanEntry.type == "product")
	{
		$("#add-product-modal-title").text(__t("Edit product on %s", mealPlanEntry.day.toString()));
		$("#day").val(mealPlanEntry.day.toString());
		Grocy.Components.ProductPicker.SetId(mealPlanEntry.product_id);
		$("#add-product-modal").modal("show");
		Grocy.FrontendHelpers.ValidateForm("add-product-form");
		Grocy.Components.ProductPicker.GetPicker().trigger("change");
	}
	else if (mealPlanEntry.type == "note")
	{
		$("#add-note-modal-title").text(__t("Edit note on %s", mealPlanEntry.day.toString()));
		$("#day").val(mealPlanEntry.day.toString());
		$("#note").val(mealPlanEntry.note);
		$("#add-note-modal").modal("show");
		Grocy.FrontendHelpers.ValidateForm("add-note-form");
	}
	Grocy.IsMealPlanEntryEditAction = true;
	Grocy.MealPlanEntryEditObjectId = mealPlanEntry.id;
});

$("#add-recipe-modal").on("shown.bs.modal", function(e)
{
	Grocy.Components.RecipePicker.GetInputElement().focus();
})

$("#add-note-modal").on("shown.bs.modal", function(e)
{
	$("#note").focus();
})

$("#add-product-modal").on("shown.bs.modal", function(e)
{
	Grocy.Components.ProductPicker.GetInputElement().focus();
})

$(document).on("click", ".remove-recipe-button, .remove-note-button, .remove-product-button", function(e)
{
	var mealPlanEntry = JSON.parse($(this).parents(".fc-h-event:first").attr("data-meal-plan-entry"));

	Grocy.Api.Delete('objects/meal_plan/' + mealPlanEntry.id.toString(), {},
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

	if (Grocy.IsMealPlanEntryEditAction)
	{
		Grocy.Api.Put('objects/meal_plan/' + Grocy.MealPlanEntryEditObjectId.toString(), $('#add-recipe-form').serializeJSON(),
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
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
	}
});

$('#save-add-note-button').on('click', function(e)
{
	e.preventDefault();

	if (document.getElementById("add-note-form").checkValidity() === false) //There is at least one validation error
	{
		return false;
	}

	var jsonData = $('#add-note-form').serializeJSON();
	jsonData.day = $("#day").val();

	if (Grocy.IsMealPlanEntryEditAction)
	{
		Grocy.Api.Put('objects/meal_plan/' + Grocy.MealPlanEntryEditObjectId.toString(), jsonData,
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('objects/meal_plan', jsonData,
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}

});

$('#save-add-product-button').on('click', function(e)
{
	e.preventDefault();

	if (document.getElementById("add-product-form").checkValidity() === false) //There is at least one validation error
	{
		return false;
	}

	var jsonData = $('#add-product-form').serializeJSON();
	jsonData.day = $("#day").val();
	delete jsonData.display_amount;
	jsonData.product_amount = jsonData.amount;
	delete jsonData.amount;
	jsonData.product_qu_id = jsonData.qu_id;
	delete jsonData.qu_id;

	if (Grocy.IsMealPlanEntryEditAction)
	{
		Grocy.Api.Put('objects/meal_plan/' + Grocy.MealPlanEntryEditObjectId.toString(), jsonData,
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('objects/meal_plan', jsonData,
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#add-recipe-form input').keydown(function(event)
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

$('#add-product-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById("add-product-form").checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$("#save-add-product-button").click();
		}
	}
});

$(document).on("keydown", "#servings", function(e)
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
	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	var objectName = SanitizeHtml($(e.currentTarget).attr('data-recipe-name'));
	var objectId = $(e.currentTarget).attr('data-recipe-id');
	var button = $(this);
	var servings = $(e.currentTarget).attr('data-mealplan-servings');

	bootbox.confirm({
		message: __t('Are you sure to put all missing ingredients for recipe "%s" on the shopping list?', objectName),
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

				// Set the recipes desired_servings so that the "recipes resolved"-views resolve correctly based on the meal plan entry servings
				Grocy.Api.Put('objects/recipes/' + objectId, { "desired_servings": servings },
					function(result)
					{
						Grocy.Api.Post('recipes/' + objectId + '/add-not-fulfilled-products-to-shoppinglist', {},
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

$(document).on('click', '.product-consume-button', function(e)
{
	e.preventDefault();

	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	Grocy.FrontendHelpers.BeginUiBusy();

	var productId = $(e.currentTarget).attr('data-product-id');
	var consumeAmount = parseFloat($(e.currentTarget).attr('data-product-amount'));

	Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': false },
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					var toastMessage = __t('Removed %1$s of %2$s from stock', consumeAmount.toString() + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse.transaction_id + '\')"><i class="fas fa-undo"></i> ' + __t("Undo") + '</a>';

					Grocy.FrontendHelpers.EndUiBusy();
					toastr.success(toastMessage);
					window.location.reload();
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy();
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy();
			console.error(xhr);
		}
	);
});

$(document).on('click', '.recipe-consume-button', function(e)
{
	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	var objectName = SanitizeHtml($(e.currentTarget).attr('data-recipe-name'));
	var objectId = $(e.currentTarget).attr('data-recipe-id');
	var servings = $(e.currentTarget).attr('data-mealplan-servings');

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

				// Set the recipes desired_servings so that the "recipes resolved"-views resolve correctly based on the meal plan entry servings
				Grocy.Api.Put('objects/recipes/' + objectId, { "desired_servings": servings },
					function(result)
					{
						Grocy.Api.Post('recipes/' + objectId + '/consume', {},
							function(result)
							{
								Grocy.FrontendHelpers.EndUiBusy();
								toastr.success(__t('Removed all ingredients of recipe "%s" from stock', objectName));
								window.location.reload();
							},
							function(xhr)
							{
								toastr.warning(__t('Not all ingredients of recipe "%s" are in stock, nothing removed', objectName));
								Grocy.FrontendHelpers.EndUiBusy();
								console.error(xhr);
							}
						);
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

$(document).on("click", ".recipe-popup-button", function(e)
{
	// Remove the focus from the current button
	// to prevent that the tooltip stays until clicked anywhere else
	document.activeElement.blur();

	var objectId = $(e.currentTarget).attr('data-recipe-id');
	var servings = $(e.currentTarget).attr('data-mealplan-servings');

	// Set the recipes desired_servings so that the "recipes resolved"-views resolve correctly based on the meal plan entry servings
	Grocy.Api.Put('objects/recipes/' + objectId, { "desired_servings": servings },
		function(result)
		{
			bootbox.dialog({
				message: '<iframe height="650px" class="embed-responsive" src="' + U("/recipes?embedded&recipe=") + objectId + '#fullscreen"></iframe>',
				size: 'extra-large',
				backdrop: true,
				closeButton: false,
				buttons: {
					cancel: {
						label: __t('Close'),
						className: 'btn-secondary responsive-button',
						callback: function()
						{
							bootbox.hideAll();
						}
					}
				}
			});
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(window).on("resize", function()
{
	// Automatically switch the calendar to "basicDay" view on small screens
	// and to "basicWeek" otherwise
	if ($(window).width() < 768)
	{
		calendar.fullCalendar("changeView", "basicDay");
	}
	else
	{
		calendar.fullCalendar("changeView", "basicWeek");
	}
});

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				Grocy.Components.ProductAmountPicker.Reload(productDetails.product.id, productDetails.quantity_unit_stock.id);

				$('#display_amount').val(1);
				$('#display_amount').focus();
				$('#display_amount').select();
				$(".input-group-productamountpicker").trigger("change");
				Grocy.FrontendHelpers.ValidateForm('add-product-form');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

function UndoStockTransaction(transactionId)
{
	Grocy.Api.Post('stock/transactions/' + transactionId.toString() + '/undo', {},
		function(result)
		{
			toastr.success(__t("Transaction successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};

Grocy.Components.RecipePicker.GetPicker().on('change', function(e)
{
	var recipeId = $(e.target).val();

	if (recipeId)
	{
		Grocy.Api.Get('objects/recipes/' + recipeId,
			function(recipe)
			{
				$("#recipe_servings").val(recipe.base_servings);
				$("#recipe_servings").focus();
				$("#recipe_servings").select();
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});
