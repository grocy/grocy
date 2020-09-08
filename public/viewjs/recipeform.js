function saveRecipePicture(result, location)
{
	$recipeId = Grocy.EditObjectId || result.created_object_id;
	Grocy.Components.UserfieldsForm.Save(() =>
	{
		if (jsonData.hasOwnProperty("picture_file_name") && !Grocy.DeleteRecipePictureOnSave)
		{
			Grocy.Api.UploadFile($("#recipe-picture")[0].files[0], 'recipepictures', jsonData.picture_file_name,
				(result) =>
				{
					window.location.href = U(location + $recipeId);
				},
				(xhr) =>
				{
					Grocy.FrontendHelpers.EndUiBusy("recipe-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			window.location.href = U(location + $recipeId);
		}
	});
}

$('.save-recipe').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#recipe-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("recipe-form");

	if ($("#recipe-picture")[0].files.length > 0)
	{
		var someRandomStuff = Math.random().toString(36).substring(2, 100) + Math.random().toString(36).substring(2, 100);
		jsonData.picture_file_name = someRandomStuff + $("#recipe-picture")[0].files[0].name;
	}

	const location = $(e.currentTarget).attr('data-location') == 'return' ? '/recipes?recipe=' : '/recipe/';

	if (Grocy.EditMode == 'create')
	{
		console.log(jsonData);
		Grocy.Api.Post('objects/recipes', jsonData,
			(result) => saveRecipePicture(result, location));
		return;
	}

	if (Grocy.DeleteRecipePictureOnSave)
	{
		jsonData.picture_file_name = null;

		Grocy.Api.DeleteFile(Grocy.RecipePictureFileName, 'recipepictures', {},
			function(result)
			{
				// Nothing to do
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}

	Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, jsonData,
		(result) => saveRecipePicture(result, location),
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("recipe-form");
			console.error(xhr);
		}
	);
});

var recipesPosTables = $('#recipes-pos-table').DataTable({
	'order': [[1, 'asc']],
	"orderFixed": [[4, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 4 }
	],
	'rowGroup': {
		dataSrc: 4
	}
});
$('#recipes-pos-table tbody').removeClass("d-none");
recipesPosTables.columns.adjust().draw();

var recipesIncludesTables = $('#recipes-includes-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
});
$('#recipes-includes-table tbody').removeClass("d-none");
recipesIncludesTables.columns.adjust().draw();

Grocy.FrontendHelpers.ValidateForm('recipe-form');
$("#name").focus();

$('#recipe-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-form');
});

$('#recipe-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('recipe-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-recipe-button').click();
		}
	}
});

$(document).on('click', '.recipe-pos-delete-button', function(e)
{
	var objectName = SanitizeHtml($(e.currentTarget).attr('data-recipe-pos-name'));
	var objectId = $(e.currentTarget).attr('data-recipe-pos-id');

	bootbox.confirm({
		message: __t('Are you sure to delete recipe ingredient "%s"?', objectName),
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
				Grocy.Api.Delete('objects/recipes_pos/' + objectId, {},
					function(result)
					{
						window.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl);
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

$(document).on('click', '.recipe-include-delete-button', function(e)
{
	var objectName = SanitizeHtml($(e.currentTarget).attr('data-recipe-include-name'));
	var objectId = $(e.currentTarget).attr('data-recipe-include-id');

	bootbox.confirm({
		message: __t('Are you sure to remove the included recipe "%s"?', objectName),
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
				Grocy.Api.Delete('objects/recipes_nestings/' + objectId, {},
					function(result)
					{
						window.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl);
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

$(document).on('click', '.recipe-pos-show-note-button', function(e)
{
	var note = SanitizeHtml($(e.currentTarget).attr('data-recipe-pos-note'));

	bootbox.alert(note);
});

$(document).on('click', '.recipe-pos-edit-button', function(e)
{
	e.preventDefault();

	var productId = $(e.currentTarget).attr("data-product-id");
	var recipePosId = $(e.currentTarget).attr('data-recipe-pos-id');

	bootbox.dialog({
		message: '<iframe height="650px" class="embed-responsive" src="' + U("/recipe/") + Grocy.EditObjectId.toString() + '/pos/' + recipePosId.toString() + '?embedded&product=' + productId.toString() + '"></iframe>',
		size: 'large',
		backdrop: true,
		closeButton: false,
		buttons: {
			cancel: {
				label: __t('Cancel'),
				className: 'btn-secondary responsive-button',
				callback: function()
				{
					bootbox.hideAll();
				}
			}
		}
	});
});

$(document).on('click', '.recipe-include-edit-button', function(e)
{
	var id = $(e.currentTarget).attr('data-recipe-include-id');
	var recipeId = $(e.currentTarget).attr('data-recipe-included-recipe-id');
	var recipeServings = $(e.currentTarget).attr('data-recipe-included-recipe-servings');

	Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			$("#recipe-include-editform-title").text(__t("Edit included recipe"));
			$("#recipe-include-form").data("edit-mode", "edit");
			$("#recipe-include-form").data("recipe-nesting-id", id);
			Grocy.Components.RecipePicker.SetId(recipeId);
			$("#includes_servings").val(recipeServings);
			$("#recipe-include-editform-modal").modal("show");
			Grocy.FrontendHelpers.ValidateForm("recipe-include-form");
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$("#recipe-pos-add-button").on("click", function(e)
{
	e.preventDefault();

	bootbox.dialog({
		message: '<iframe height="650px" class="embed-responsive" src="' + U("/recipe/") + Grocy.EditObjectId + '/pos/new?embedded"></iframe>',
		size: 'large',
		backdrop: true,
		closeButton: false,
		buttons: {
			cancel: {
				label: __t('Cancel'),
				className: 'btn-secondary responsive-button',
				callback: function()
				{
					bootbox.hideAll();
				}
			}
		}
	});
});

$("#recipe-include-add-button").on("click", function(e)
{
	Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			$("#recipe-include-editform-title").text(__t("Add included recipe"));
			$("#recipe-include-form").data("edit-mode", "create");
			Grocy.Components.RecipePicker.Clear();
			$("#recipe-include-editform-modal").modal("show");
			Grocy.FrontendHelpers.ValidateForm("recipe-include-form");
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$('#save-recipe-include-button').on('click', function(e)
{
	e.preventDefault();

	if (document.getElementById("recipe-include-form").checkValidity() === false) //There is at least one validation error
	{
		return false;
	}

	var nestingId = $("#recipe-include-form").data("recipe-nesting-id");
	var editMode = $("#recipe-include-form").data("edit-mode");

	var jsonData = {};
	jsonData.includes_recipe_id = Grocy.Components.RecipePicker.GetValue();
	jsonData.servings = $("#includes_servings").val();
	jsonData.recipe_id = Grocy.EditObjectId;

	if (editMode === 'create')
	{
		Grocy.Api.Post('objects/recipes_nestings', jsonData,
			function(result)
			{
				window.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/recipes_nestings/' + nestingId, jsonData,
			function(result)
			{
				window.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$("#recipe-picture").on("change", function(e)
{
	$("#recipe-picture-label").removeClass("d-none");
	$("#recipe-picture-label-none").addClass("d-none");
	$("#delete-current-recipe-picture-on-save-hint").addClass("d-none");
	$("#current-recipe-picture").addClass("d-none");
	Grocy.DeleteRecipePictureOnSave = false;
});

Grocy.DeleteRecipePictureOnSave = false;
$("#delete-current-recipe-picture-button").on("click", function(e)
{
	Grocy.DeleteRecipePictureOnSave = true;
	$("#current-recipe-picture").addClass("d-none");
	$("#delete-current-recipe-picture-on-save-hint").removeClass("d-none");
	$("#recipe-picture-label").addClass("d-none");
	$("#recipe-picture-label-none").removeClass("d-none");
});

Grocy.Components.UserfieldsForm.Load();

$(window).on("message", function(e)
{
	var data = e.originalEvent.data;

	if (data.Message === "IngredientsChanged")
	{
		Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/recipe/' + Grocy.EditObjectId);
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

// Grocy.Components.RecipePicker.GetPicker().on('change', function (e)
// {
// 	var value = Grocy.Components.RecipePicker.GetValue();
// 	if (value.toString().isEmpty())
// 	{
// 		return;
// 	}

// 	Grocy.Api.Get('objects/recipes/' + value,
// 		function(recipe)
// 		{
// 			$("#includes_servings").val(recipe.servings);
// 		},
// 		function(xhr)
// 		{
// 			console.error(xhr);
// 		}
// 	);
// });

// Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
// {
// 	// Just save the current recipe on every change of the product picker as a workflow could be started which leaves the page...
// 	Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function () { }, function () { });
// });

// As the /recipe/new route immediately creates a new recipe on load,
// always replace the current location by the created recipes edit page location
// if (window.location.pathname.toLowerCase() === "/recipe/new")
// {
// 	window.history.replaceState(null, null, U("/recipe/" + Grocy.EditObjectId));
// }
