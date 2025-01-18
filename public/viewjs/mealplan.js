var firstRender = true;
Grocy.IsMealPlanEntryEditAction = false;

var firstDay = null;
if (Grocy.CalendarFirstDayOfWeek)
{
	firstDay = Number.parseInt(Grocy.CalendarFirstDayOfWeek);
}
if (Grocy.MealPlanFirstDayOfWeek)
{
	firstDay = Number.parseInt(Grocy.MealPlanFirstDayOfWeek);

	if (firstDay == -1)
	{
		firstDay = moment().day();
	}
}

$(".calendar").each(function()
{
	var container = $(this);
	var sectionId = container.attr("data-section-id");
	var sectionName = container.attr("data-section-name");
	var isPrimarySection = BoolVal(container.attr("data-primary-section"));
	var isLastSection = BoolVal(container.attr("data-last-section"));

	var rightButtonList = "agendaWeek,agendaDay,prev,today,next";
	if ($(window).width() < 768)
	{
		var rightButtonList = "prev,today,next";
	}

	var headerConfig = {
		"left": "title",
		"center": "",
		"right": rightButtonList
	};

	if (!isPrimarySection)
	{
		headerConfig = {
			"left": "",
			"center": "",
			"right": ""
		};
	}

	container.fullCalendar({
		"themeSystem": "bootstrap4",
		"header": headerConfig,
		"weekNumbers": false,
		"eventLimit": false,
		"eventSources": fullcalendarEventSources,
		"defaultView": ($(window).width() < 768 || GetUriParam("days") == "0") ? "agendaDay" : "agendaWeek",
		"allDayText": sectionName,
		"allDayHtml": sectionName,
		"minTime": "00:00:00",
		"maxTime": "00:00:01",
		"scrollTime": "00:00:00",
		"firstDay": firstDay,
		"height": "auto",
		"defaultDate": GetUriParam("start"),
		"viewRender": function(view)
		{
			if (!isPrimarySection)
			{
				return;
			}

			$(".calendar[data-primary-section='true'] .fc-day-header").prepend('\
			<div class="btn-group mr-2 my-1 d-print-none"> \
				<button type="button" class="btn btn-outline-dark btn-xs add-recipe-button" data-toggle="tooltip" title="' + __t('Add recipe') + '"><i class="fa-solid fa-plus"></i></a></button> \
				<button type="button" class="btn btn-outline-dark btn-xs dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button> \
				<div class="table-inline-menu dropdown-menu"> \
					<a class="dropdown-item add-note-button" href="#"><span class="dropdown-item-text">' + __t('Add note') + '</span></a> \
					<a class="dropdown-item add-product-button" href="#"><span class="dropdown-item-text">' + __t('Add product') + '</span></a> \
					<a class="dropdown-item copy-day-button" href="#"><span class="dropdown-item-text">' + __t('Copy this day') + '</span></a> \
				</div> \
			</div>');

			var weekCosts = 0;
			var weekRecipeOrderMissingButtonHtml = "";
			var weekRecipeConsumeButtonHtml = "";
			var weekCostsHtml = "";
			if (weekRecipe !== null)
			{
				var weekRecipeResolved = FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", weekRecipe.id);

				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
				{
					weekCosts = weekRecipeResolved.costs;
					weekCostsHtml = __t("Week costs") + ': <span class="locale-number locale-number-currency">' + weekCosts.toString() + "</span> ";
				}

				var weekRecipeOrderMissingButtonDisabledClasses = "";
				if (weekRecipeResolved.need_fulfilled_with_shopping_list == 1)
				{
					weekRecipeOrderMissingButtonDisabledClasses = "disabled";
				}

				var weekRecipeOrderMissingButtonHtml = "";
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_SHOPPINGLIST)
				{
					weekRecipeOrderMissingButtonHtml = '<a class="ml-2 btn btn-outline-primary btn-xs recipe-order-missing-button d-print-none ' + weekRecipeOrderMissingButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Put missing products on shopping list") + '" data-recipe-id="' + weekRecipe.id.toString() + '" data-recipe-name="' + weekRecipe.name + '" data-recipe-type="' + weekRecipe.type + '"><i class="fa-solid fa-cart-plus"></i></a>';
				}

				weekRecipeConsumeButtonHtml = '<a class="ml-2 btn btn-outline-success btn-xs recipe-consume-button d-print-none" href="#" data-toggle="tooltip" title="' + __t("Consume all ingredients needed by this weeks recipes or products") + '" data-recipe-id="' + weekRecipe.id.toString() + '" data-recipe-name="' + weekRecipe.name + '" data-recipe-type="' + weekRecipe.type + '"><i class="fa-solid fa-utensils"></i></a>'
			}
			$(".calendar[data-primary-section='true'] .fc-header-toolbar .fc-center").html("<h4>" + weekCostsHtml + weekRecipeOrderMissingButtonHtml + weekRecipeConsumeButtonHtml + "</h4>");
		},
		"eventRender": function(event, element)
		{
			element.removeClass("fc-event");
			element.addClass("text-center");
			element.attr("data-meal-plan-entry", event.mealPlanEntry);
			element.addClass("discrete-link");

			var mealPlanEntry = JSON.parse(event.mealPlanEntry);

			if (sectionId != mealPlanEntry.section_id)
			{
				return false;
			}

			var additionalTitleCssClasses = "";
			var doneButtonHtml = '<a class="ml-2 btn btn-outline-secondary btn-xs mealplan-entry-done-button" href="#" data-toggle="tooltip" title="' + __t("Mark this item as done") + '" data-mealplan-entry-id="' + mealPlanEntry.id.toString() + '"><i class="fa-solid fa-check"></i></a>';
			if (BoolVal(mealPlanEntry.done))
			{
				additionalTitleCssClasses = "text-strike-through text-muted";
				doneButtonHtml = '<a class="ml-2 btn btn-outline-secondary btn-xs mealplan-entry-undone-button" href="#" data-toggle="tooltip" title="' + __t("Mark this item as undone") + '" data-mealplan-entry-id="' + mealPlanEntry.id.toString() + '"><i class="fa-solid fa-undo"></i></a>';
			}

			if (event.type == "recipe")
			{
				var recipe = JSON.parse(event.recipe);
				if (recipe === null || recipe === undefined)
				{
					return false;
				}

				recipe.name = recipe.name.escapeHTML();

				var internalShadowRecipe = FindObjectInArrayByPropertyValue(internalRecipes, "name", mealPlanEntry.day + "#" + mealPlanEntry.id);
				var resolvedRecipe = FindObjectInArrayByPropertyValue(recipesResolved, "recipe_id", internalShadowRecipe.id);

				element.attr("data-recipe", event.recipe);

				var recipeOrderMissingButtonDisabledClasses = "";
				if (resolvedRecipe.need_fulfilled_with_shopping_list == 1)
				{
					recipeOrderMissingButtonDisabledClasses = "disabled";
				}

				var fulfillmentInfoHtml = __t('Enough in stock');
				var fulfillmentIconHtml = '<i class="fa-solid fa-check text-success"></i>';
				if (resolvedRecipe.need_fulfilled != 1)
				{
					fulfillmentInfoHtml = __t('Not enough in stock');
					var fulfillmentIconHtml = '<i class="fa-solid fa-times text-danger"></i>';
				}
				var costsAndCaloriesPerServing = ""
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
				{
					costsAndCaloriesPerServing = '<h5 class="small text-truncate mb-1"><span class="locale-number locale-number-currency">' + resolvedRecipe.costs + '</span> / <span class="locale-number locale-number-generic">' + resolvedRecipe.calories / mealPlanEntry.recipe_servings + '</span> ' + Grocy.EnergyUnit + ' ' + __t('per serving') + '</h5>';
				}
				else
				{
					costsAndCaloriesPerServing = '<h5 class="small text-truncate mb-1"><span class="locale-number locale-number-generic">' + resolvedRecipe.calories / mealPlanEntry.recipe_servings + '</span> ' + Grocy.EnergyUnit + ' ' + __t('per serving') + '</h5>';
				}

				if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK)
				{
					fulfillmentIconHtml = "";
					fulfillmentInfoHtml = "";
				}

				var shoppingListButtonHtml = "";
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_SHOPPINGLIST)
				{
					shoppingListButtonHtml = '<a class="btn btn-outline-primary btn-xs recipe-order-missing-button ' + recipeOrderMissingButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Put missing products on shopping list") + '" data-recipe-id="' + recipe.id.toString() + '" data-mealplan-servings="' + mealPlanEntry.recipe_servings + '" data-recipe-name="' + recipe.name + '" data-recipe-type="' + recipe.type + '"><i class="fa-solid fa-cart-plus"></i></a>';
				}

				element.html('\
				<div> \
					<h5 class="text-truncate mb-1 cursor-link display-recipe-button ' + additionalTitleCssClasses + '" data-toggle="tooltip" title="' + __t("Display recipe") + '" data-recipe-id="' + recipe.id.toString() + '" data-recipe-name="' + recipe.name + '" data-mealplan-servings="' + mealPlanEntry.recipe_servings + '" data-recipe-type="' + recipe.type + '">' + recipe.name + '</h5> \
					<h5 class="small text-truncate mb-1">' + __n(mealPlanEntry.recipe_servings, "%s serving", "%s servings") + '</h5> \
					<h5 class="small timeago-contextual text-truncate mb-1">' + fulfillmentIconHtml + " " + fulfillmentInfoHtml + '</h5> \
					' + costsAndCaloriesPerServing + ' \
					<h5 class="d-print-none"> \
						<a class="ml-2 btn btn-outline-info btn-xs edit-meal-plan-entry-button" href="#" data-toggle="tooltip" title="' + __t("Edit this item") + '"><i class="fa-solid fa-edit"></i></a> \
						<a class="btn btn-outline-danger btn-xs remove-recipe-button" href="#" data-toggle="tooltip" title="' + __t("Delete this item") + '"><i class="fa-solid fa-trash"></i></a> \
						<a class="ml-2 btn btn-outline-success btn-xs recipe-consume-button" href="#" data-toggle="tooltip" title="' + __t("Consume all ingredients needed by this recipe") + '" data-recipe-id="' + internalShadowRecipe.id.toString() + '" data-mealplan-entry-id="' + mealPlanEntry.id.toString() + '" data-recipe-name="' + recipe.name + '" data-recipe-type="' + recipe.type + '"><i class="fa-solid fa-utensils"></i></a> \
						' + shoppingListButtonHtml + ' \
						' + doneButtonHtml + ' \
					</h5> \
				</div>');

				if (recipe.picture_file_name)
				{
					element.prepend('<div class="mx-auto mb-1"><img src="' + U("/api/files/recipepictures/") + btoa(recipe.picture_file_name) + '?force_serve_as=picture&best_fit_width=400" class="img-fluid rounded-circle" loading="lazy"></div>')
				}
			}
			else if (event.type == "product")
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

				element.attr("data-product-details", event.productDetails);

				var productOrderMissingButtonDisabledClasses = "disabled";
				if (productDetails.stock_amount_aggregated < mealPlanEntry.product_amount)
				{
					productOrderMissingButtonDisabledClasses = "";
				}

				var productConsumeButtonDisabledClasses = "disabled";
				if (productDetails.stock_amount_aggregated >= mealPlanEntry.product_amount)
				{
					productConsumeButtonDisabledClasses = "";
				}

				fulfillmentInfoHtml = __t('Not enough in stock');
				var fulfillmentIconHtml = '<i class="fa-solid fa-times text-danger"></i>';
				if (productDetails.stock_amount_aggregated >= mealPlanEntry.product_amount)
				{
					var fulfillmentInfoHtml = __t('Enough in stock');
					var fulfillmentIconHtml = '<i class="fa-solid fa-check text-success"></i>';
				}

				var costsAndCaloriesPerServing = ""
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING)
				{
					costsAndCaloriesPerServing = '<h5 class="small text-truncate mb-1"><span class="locale-number locale-number-currency">' + productDetails.last_price * mealPlanEntry.product_amount + '</span> / <span class="locale-number locale-number-generic">' + productDetails.product.calories + '</span> ' + Grocy.EnergyUnit + ' </h5>';
				}
				else
				{
					costsAndCaloriesPerServing = '<h5 class="small text-truncate mb-1"><span class="locale-number locale-number-generic">' + productDetails.product.calories + '</span> ' + Grocy.EnergyUnit + ' </h5>';
				}

				var shoppingListButtonHtml = "";
				if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_SHOPPINGLIST)
				{
					shoppingListButtonHtml = '<a class="btn btn-outline-primary btn-xs show-as-dialog-link ' + productOrderMissingButtonDisabledClasses + '" href="' + U("/shoppinglistitem/new?embedded&updateexistingproduct&list=1&product=") + mealPlanEntry.product_id + '&amount=' + mealPlanEntry.product_amount + '" data-toggle="tooltip" title="' + __t("Add to shopping list") + '" data-product-id="' + productDetails.product.id.toString() + '" data-product-name="' + productDetails.product.name + '" data-product-amount="' + mealPlanEntry.product_amount + '"><i class="fa-solid fa-cart-plus"></i></a>';
				}

				element.html('\
				<div> \
					<h5 class="text-truncate mb-1 cursor-link productcard-trigger ' + additionalTitleCssClasses + '" data-toggle="tooltip" title="' + __t("Display product") + '" data-product-id="' + productDetails.product.id.toString() + '">' + productDetails.product.name + '</h5> \
					<h5 class="small text-truncate mb-1"><span class="locale-number locale-number-quantity-amount">' + mealPlanEntry.product_amount + "</span> " + __n(mealPlanEntry.product_amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural, true) + '</h5> \
					<h5 class="small timeago-contextual text-truncate mb-1">' + fulfillmentIconHtml + " " + fulfillmentInfoHtml + '</h5> \
					' + costsAndCaloriesPerServing + ' \
					<h5 class="d-print-none"> \
						<a class="btn btn-outline-info btn-xs edit-meal-plan-entry-button" href="#" data-toggle="tooltip" title="' + __t("Edit this item") + '"><i class="fa-solid fa-edit"></i></a> \
						<a class="btn btn-outline-danger btn-xs remove-product-button" href="#" data-toggle="tooltip" title="' + __t("Delete this item") + '"><i class="fa-solid fa-trash"></i></a> \
						<a class="ml-2 btn btn-outline-success btn-xs product-consume-button ' + productConsumeButtonDisabledClasses + '" href="#" data-toggle="tooltip" title="' + __t("Consume %1$s of %2$s", mealPlanEntry.product_amount.toLocaleString() + ' ' + __n(mealPlanEntry.product_amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural, true), productDetails.product.name) + '" data-product-id="' + productDetails.product.id.toString() + '" data-product-name="' + productDetails.product.name + '" data-product-amount="' + mealPlanEntry.product_amount + '" data-mealplan-entry-id="' + mealPlanEntry.id.toString() + '"><i class="fa-solid fa-utensils"></i></a> \
						' + shoppingListButtonHtml + ' \
						' + doneButtonHtml + ' \
					</h5> \
				</div>');

				if (productDetails.product.picture_file_name)
				{
					element.prepend('<div class="mx-auto mb-1"><img src="' + U("/api/files/productpictures/") + btoa(productDetails.product.picture_file_name) + '?force_serve_as=picture&best_fit_width=400" class="img-fluid rounded-circle" loading="lazy"></div>')
				}
			}
			else if (event.type == "note")
			{
				element.html('\
				<div> \
					<h5 class="text-wrap text-break mb-1 ' + additionalTitleCssClasses + '">' + mealPlanEntry.note + '</h5> \
					<h5 class="d-print-none"> \
						<a class="btn btn-outline-info btn-xs edit-meal-plan-entry-button" href="#" data-toggle="tooltip" title="' + __t("Edit this item") + '"><i class="fa-solid fa-edit"></i></a> \
						<a class="btn btn-outline-danger btn-xs remove-note-button" href="#" data-toggle="tooltip" title="' + __t("Delete this item") + '"><i class="fa-solid fa-trash"></i></a> \
						' + doneButtonHtml + ' \
					</h5> \
				</div>');
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
						costsAndCaloriesPerDay = '<h5 class="small text-truncate"><span class="locale-number locale-number-currency">' + dayRecipeResolved.costs + '</span> / <span class="locale-number locale-number-generic">' + dayRecipeResolved.calories + '</span> ' + Grocy.EnergyUnit + ' ' + __t('per day') + '</h5>';
					}
					else
					{
						costsAndCaloriesPerDay = '<h5 class="small text-truncate"><span class="locale-number locale-number-generic">' + dayRecipeResolved.calories + '</span> ' + Grocy.EnergyUnit + ' ' + __t('per day') + '</h5>';
					}

					$(".calendar[data-primary-section='true'] .fc-day-header[data-date='" + dayRecipeName + "']").append('<h5 id="day-summary-' + dayRecipeName + '" class="small text-truncate border-top pt-1 pb-0">' + costsAndCaloriesPerDay + '</h5>');
				}
			}
		},
		"eventAfterAllRender": function(view)
		{
			if (isPrimarySection)
			{
				UpdateUriParam("start", view.start.format("YYYY-MM-DD"));

				if (view.name == "agendaDay")
				{
					UpdateUriParam("days", "0");
				}
				else
				{
					RemoveUriParam("days");
				}

				if (firstRender)
				{
					firstRender = false
				}
				else
				{
					$(".calendar").addClass("d-none");
					window.location.reload();
					return false;
				}
			}

			if (isLastSection)
			{
				$(".fc-axis span").replaceWith(function()
				{
					return $("<div />", { html: $(this).html() });
				});

				RefreshLocaleNumberDisplay();
				$('[data-toggle="tooltip"]').tooltip();

				if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK)
				{
					$(".recipe-order-missing-button").addClass("d-none");
					$(".recipe-consume-button").addClass("d-none");
				}
			}
		}
	});
});

$(document).on("click", ".add-recipe-button", function(e)
{
	var day = $(this).parent().parent().data("date");

	$("#add-recipe-modal-title").text(__t("Add meal plan entry"));
	$(".datetimepicker-wrapper").detach().prependTo("#add-recipe-form");
	$("input#day").detach().appendTo("#add-recipe-form");
	Grocy.Components.DateTimePicker.Init(true);
	Grocy.Components.DateTimePicker.SetValue(day);
	Grocy.Components.RecipePicker.Clear();
	$("#section_id_note").val(-1);
	$("#add-recipe-modal").modal("show");
	Grocy.FrontendHelpers.ValidateForm("add-recipe-form");
	Grocy.IsMealPlanEntryEditAction = false;
});

$(document).on("click", ".add-note-button", function(e)
{
	var day = $(this).parent().parent().parent().data("date");

	$("#add-note-modal-title").text(__t("Add meal plan entry"));
	$(".datetimepicker-wrapper").detach().prependTo("#add-note-form");
	$("input#day").detach().appendTo("#add-note-form")
	Grocy.Components.DateTimePicker.Init(true);
	Grocy.Components.DateTimePicker.SetValue(day);
	$("#note").val("");
	$("#section_id_note").val(-1);
	$("#add-note-modal").modal("show");
	Grocy.FrontendHelpers.ValidateForm("add-note-form");
	Grocy.IsMealPlanEntryEditAction = false;
});

$(document).on("click", ".add-product-button", function(e)
{
	var day = $(this).parent().parent().parent().data("date");

	$("#add-product-modal-title").text(__t("Add meal plan entry"));
	$(".datetimepicker-wrapper").detach().prependTo("#add-product-form");
	$("input#day").detach().appendTo("#add-product-form")
	Grocy.Components.DateTimePicker.Init(true);
	Grocy.Components.DateTimePicker.SetValue(day);
	Grocy.Components.ProductPicker.Clear();
	$("#section_id_note").val(-1);
	$("#add-product-modal").modal("show");
	Grocy.FrontendHelpers.ValidateForm("add-product-form");
	Grocy.IsMealPlanEntryEditAction = false;
});

$(document).on("click", ".edit-meal-plan-entry-button", function(e)
{
	var mealPlanEntry = JSON.parse($(this).parents(".fc-h-event:first").attr("data-meal-plan-entry"));

	if (mealPlanEntry.type == "recipe")
	{
		$(".datetimepicker-wrapper").detach().prependTo("#add-recipe-form");
		$("input#day").detach().appendTo("#add-recipe-form")
		Grocy.Components.DateTimePicker.Init(true);
		Grocy.Components.DateTimePicker.SetValue(mealPlanEntry.day);
		$("#add-recipe-modal-title").text(__t("Edit meal plan entry"));
		$("#recipe_servings").val(mealPlanEntry.recipe_servings);
		Grocy.Components.RecipePicker.SetId(mealPlanEntry.recipe_id);
		$("#add-recipe-modal").modal("show");
		$("#section_id_recipe").val(mealPlanEntry.section_id);
		Grocy.FrontendHelpers.ValidateForm("add-recipe-form");
	}
	else if (mealPlanEntry.type == "product")
	{
		$(".datetimepicker-wrapper").detach().prependTo("#add-product-form");
		$("input#day").detach().appendTo("#add-product-form")
		Grocy.Components.DateTimePicker.Init(true);
		Grocy.Components.DateTimePicker.SetValue(mealPlanEntry.day);
		$("#add-product-modal-title").text(__t("Edit meal plan entry"));
		Grocy.Components.ProductPicker.SetId(mealPlanEntry.product_id);
		$("#add-product-modal").modal("show");
		$("#section_id_product").val(mealPlanEntry.section_id);
		Grocy.FrontendHelpers.ValidateForm("add-product-form");
		Grocy.Components.ProductPicker.GetPicker().trigger("change");
	}
	else if (mealPlanEntry.type == "note")
	{
		$(".datetimepicker-wrapper").detach().prependTo("#add-note-form");
		$("input#day").detach().appendTo("#add-note-form");
		Grocy.Components.DateTimePicker.Init(true);
		Grocy.Components.DateTimePicker.SetValue(mealPlanEntry.day);
		$("#add-note-modal-title").text(__t("Edit meal plan entry"));
		$("#note").val(mealPlanEntry.note);
		$("#add-note-modal").modal("show");
		$("#section_id_note").val(mealPlanEntry.section_id);
		Grocy.FrontendHelpers.ValidateForm("add-note-form");
	}
	Grocy.IsMealPlanEntryEditAction = true;
	Grocy.MealPlanEntryEditObject = mealPlanEntry;
});

$(document).on("click", ".copy-day-button", function(e)
{
	var day = $(this).parent().parent().parent().data("date");

	$("#copy-day-modal-title").text(__t("Copy all meal plan entries of %s", day.toString()));
	Grocy.Components.DateTimePicker.SetValue(day);
	Grocy.Components.DateTimePicker2.Clear();
	$("#copy-day-modal").modal("show");
	Grocy.FrontendHelpers.ValidateForm("copy-day-form");
	Grocy.IsMealPlanEntryEditAction = false;
});

$("#add-recipe-modal").on("shown.bs.modal", function(e)
{
	if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_DISABLE_BROWSER_BARCODE_CAMERA_SCANNING)
	{
		Grocy.Components.CameraBarcodeScanner.Init();
	}

	Grocy.Components.RecipePicker.GetInputElement().focus();
});

$("#add-note-modal").on("shown.bs.modal", function(e)
{
	$("#note").focus();
});

$("#add-product-modal").on("shown.bs.modal", function(e)
{
	if (!Grocy.FeatureFlags.GROCY_FEATURE_FLAG_DISABLE_BROWSER_BARCODE_CAMERA_SCANNING)
	{
		Grocy.Components.CameraBarcodeScanner.Init();
	}

	Grocy.Components.ProductPicker.GetInputElement().focus();
});

$("#copy-day-modal").on("shown.bs.modal", function(e)
{
	Grocy.Components.DateTimePicker2.GetInputElement().focus();
});

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
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
		}
	);
});

$('#save-add-recipe-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("add-recipe-form", true) || $(".combobox-menu-visible").length)
	{
		return false;
	}

	var formData = $('#add-recipe-form').serializeJSON();
	formData.section_id = formData.section_id_recipe;
	delete formData.section_id_recipe;
	formData.day = Grocy.Components.DateTimePicker.GetValue();

	if (Grocy.IsMealPlanEntryEditAction)
	{
		Grocy.Api.Put('objects/meal_plan/' + Grocy.MealPlanEntryEditObject.id, formData,
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
	else
	{
		Grocy.Api.Post('objects/meal_plan', formData,
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
});

$('#save-add-note-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("add-note-form", true) || $(".combobox-menu-visible").length)
	{
		return false;
	}

	var jsonData = $('#add-note-form').serializeJSON();
	jsonData.day = Grocy.Components.DateTimePicker.GetValue();
	jsonData.section_id = jsonData.section_id_note;
	delete jsonData.section_id_note;

	if (Grocy.IsMealPlanEntryEditAction)
	{
		Grocy.Api.Put('objects/meal_plan/' + Grocy.MealPlanEntryEditObject.id, jsonData,
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
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
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}

});

$('#save-add-product-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("add-product-form", true) || $(".combobox-menu-visible").length)
	{
		return false;
	}

	var jsonData = $('#add-product-form').serializeJSON();
	jsonData.day = Grocy.Components.DateTimePicker.GetValue();
	delete jsonData.display_amount;
	jsonData.product_amount = jsonData.amount;
	delete jsonData.amount;
	jsonData.product_qu_id = $("#qu_id").val();
	delete jsonData.qu_id;
	jsonData.section_id = jsonData.section_id_product;
	delete jsonData.section_id_product;

	if (Grocy.IsMealPlanEntryEditAction)
	{
		Grocy.Api.Put('objects/meal_plan/' + Grocy.MealPlanEntryEditObject.id, jsonData,
			function(result)
			{
				window.location.reload();
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
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
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
});

var itemsToCopy = 0;
var itemsCopied = 0;
$('#save-copy-day-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("copy-day-form", true))
	{
		return false;
	}

	var dayFrom = Grocy.Components.DateTimePicker.GetValue();
	var dayTo = Grocy.Components.DateTimePicker2.GetValue();

	Grocy.Api.Get('objects/meal_plan?query[]=day=' + dayFrom,
		function(sourceMealPlanEntries)
		{
			itemsToCopy = sourceMealPlanEntries.length;

			sourceMealPlanEntries.forEach((item) =>
			{
				item.day = dayTo;
				item.done = 0;
				delete item.id;
				delete item.row_created_timestamp;

				Grocy.Api.Post("objects/meal_plan", item,
					function(result)
					{
						itemsCopied++;

						if (itemsCopied == itemsToCopy)
						{
							window.location.reload();
						}
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
					}
				);
			});

			//window.location.reload();
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$('#add-recipe-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('add-recipe-form'))
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
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('add-product-form'))
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
	if (e.keyCode === 13) // Enter
	{
		e.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('add-recipe-form'))
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
	var servings = $(e.currentTarget).attr('data-mealplan-servings');

	bootbox.confirm({
		message: __t('Are you sure you want to put all missing ingredients for recipe "%s" on the shopping list?', objectName),
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

	Grocy.FrontendHelpers.BeginUiBusy();

	var productId = $(e.currentTarget).attr('data-product-id');
	var consumeAmount = Number.parseFloat($(e.currentTarget).attr('data-product-amount'));
	var mealPlanEntryId = $(e.currentTarget).attr('data-mealplan-entry-id');

	Grocy.Api.Post('stock/products/' + productId + '/consume', { 'amount': consumeAmount, 'spoiled': false },
		function(bookingResponse)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(result)
				{
					var toastMessage = __t('Removed %1$s of %2$s from stock', consumeAmount.toString() + " " + __n(consumeAmount, result.quantity_unit_stock.name, result.quantity_unit_stock.name_plural, true), result.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockTransaction(\'' + bookingResponse[0].transaction_id + '\')"><i class="fa-solid fa-undo"></i> ' + __t("Undo") + '</a>';

					Grocy.Api.Put('objects/meal_plan/' + mealPlanEntryId, { "done": 1 },
						function(result)
						{
							Grocy.FrontendHelpers.EndUiBusy();
							toastr.success(toastMessage);
							window.location.reload();
						},
						function(xhr)
						{
							Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
						}
					);
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
	var objectName = $(e.currentTarget).attr('data-recipe-name');
	var objectId = $(e.currentTarget).attr('data-recipe-id');
	var mealPlanEntryId = $(e.currentTarget).attr('data-mealplan-entry-id');

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
						Grocy.Api.Put('objects/meal_plan/' + mealPlanEntryId, { "done": 1 },
							function(result)
							{
								Grocy.FrontendHelpers.EndUiBusy();
								toastr.success(__t('Removed all in stock ingredients needed by recipe \"%s\" from stock', objectName));
								window.location.reload();
							},
							function(xhr)
							{
								Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
							}
						);
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

$(document).on("click", ".display-recipe-button", function(e)
{
	var objectId = $(e.currentTarget).attr('data-recipe-id');
	var servings = $(e.currentTarget).attr('data-mealplan-servings');

	// Set the recipes desired_servings so that the "recipes resolved"-views resolve correctly based on the meal plan entry servings
	Grocy.Api.Put('objects/recipes/' + objectId, { "desired_servings": servings },
		function(result)
		{
			$("body").addClass("fullscreen-card");

			bootbox.dialog({
				message: '<iframe class="embed-responsive" src="' + U("/recipes?embedded&recipe=") + objectId + '#fullscreen"></iframe>',
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
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on("click", ".mealplan-entry-done-button", function(e)
{
	e.preventDefault();

	var mealPlanEntryId = $(e.currentTarget).attr("data-mealplan-entry-id");
	Grocy.Api.Put("objects/meal_plan/" + mealPlanEntryId, { "done": 1 },
		function(result)
		{
			window.location.reload();
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
		}
	);
});

$(document).on("click", ".mealplan-entry-undone-button", function(e)
{
	e.preventDefault();

	var mealPlanEntryId = $(e.currentTarget).attr("data-mealplan-entry-id");
	Grocy.Api.Put("objects/meal_plan/" + mealPlanEntryId, { "done": 0 },
		function(result)
		{
			window.location.reload();
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
		}
	);
});

$(window).one("resize", function()
{
	// Automatically switch the calendar to "agendaDay" view on small screens and to "agendaWeek" otherwise
	var windowWidth = $(window).width();
	$(".calendar").each(function()
	{
		if (windowWidth < 768)
		{
			$(this).fullCalendar("changeView", "agendaDay");
		}
		else
		{
			$(this).fullCalendar("changeView", "agendaWeek");
		}
	});
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
				Grocy.Components.ProductAmountPicker.SetQuantityUnit(productDetails.quantity_unit_stock.id);

				if (Grocy.IsMealPlanEntryEditAction)
				{
					$('#display_amount').val(Grocy.MealPlanEntryEditObject.product_amount);
				}
				else
				{
					$('#display_amount').val(1);
				}

				RefreshLocaleNumberInput();
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

$("#print-meal-plan-button").on("click", function(e)
{
	window.print();
});
