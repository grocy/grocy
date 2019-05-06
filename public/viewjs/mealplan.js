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
	"viewRender": function(view)
	{
		$(".fc-day-header").append('<a class="ml-1 btn btn-outline-dark btn-xs my-1 add-recipe-button" href="#"><i class="fas fa-plus"></i></a>');
	},
	"eventRender": function(event, element)
	{
		var recipe = JSON.parse(event.recipe);

		element.removeClass("fc-event");
		element.addClass("text-center");
		element.attr("data-recipe", event.recipe);
		element.attr("data-meal-plan-entry", event.mealPlanEntry);
		element.html('<h5 class="text-truncate">' + recipe.name + '<br><a class="ml-1 btn btn-outline-danger btn-xs remove-recipe-button" href="#"><i class="fas fa-trash"></i></a></h5>');
		if (recipe.picture_file_name && !recipe.picture_file_name.isEmpty())
		{
			element.html(element.html() + '<img src="' + U("/api/files/recipepictures/") + btoa(recipe.picture_file_name) + '" class="img-fluid">')
		}
	}
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
	var mealPlanEntry = JSON.parse($(this).parent().parent().attr("data-meal-plan-entry"));

	Grocy.Api.Delete('objects/meal_plan/' + mealPlanEntry.id.toString(), { },
		function(result)
		{
			calendar.fullCalendar('removeEvents', [mealPlanEntry.id]);
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
