$('#save-recipe-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#recipe-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("recipe-form");

	if ($("#recipe-picture")[0].files.length > 0)
	{
		var someRandomStuff = Math.random().toString(36).substring(2, 100) + Math.random().toString(36).substring(2, 100);
		jsonData.picture_file_name = someRandomStuff + $("#recipe-picture")[0].files[0].name;
	}

	if (Grocy.DeleteRecipePictureOnSave)
	{
		jsonData.picture_file_name = null;

		Grocy.Api.DeleteFile(Grocy.RecipePictureFileName, 'recipepictures', {},
			function (result)
			{
				// Nothing to do
			},
			function (xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}

	Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, jsonData,
		function(result)
		{
			Grocy.Components.UserfieldsForm.Save(function()
			{
				if (jsonData.hasOwnProperty("picture_file_name") && !Grocy.DeleteRecipePictureOnSave)
				{
					Grocy.Api.UploadFile($("#recipe-picture")[0].files[0], 'recipepictures', jsonData.picture_file_name,
						function (result)
						{
							window.location.href = U('/recipes?recipe=' + Grocy.EditObjectId);
						},
						function (xhr)
						{
							Grocy.FrontendHelpers.EndUiBusy("recipe-form");
							Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
						}
					);
				}
				else
				{
					window.location.href = U('/recipes?recipe=' + Grocy.EditObjectId);
				}
			});
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("recipe-form");
			console.error(xhr);
		}
	);
});

var recipesPosTables = $('#recipes-pos-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	"orderFixed": [[4, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'visible': false, 'targets': 4 }
	],
	'language': JSON.parse(__t('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	},
	'rowGroup': {
		dataSrc: 4
	}
});
$('#recipes-pos-table tbody').removeClass("d-none");
recipesPosTables.columns.adjust().draw();

var recipesIncludesTables = $('#recipes-includes-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(__t('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function (settings, data)
	{
		data.search.search = "";

		data.columns.forEach(column =>
		{
			column.search.search = "";
		});
	}
});
$('#recipes-includes-table tbody').removeClass("d-none");
recipesIncludesTables.columns.adjust().draw();

Grocy.FrontendHelpers.ValidateForm('recipe-form');
$("#name").focus();

$('#recipe-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('recipe-form');
});

$('#recipe-form input').keydown(function (event)
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
	var objectName = $(e.currentTarget).attr('data-recipe-pos-name');
	var objectId = $(e.currentTarget).attr('data-recipe-pos-id');

	bootbox.confirm({
		message: __t('Are you sure to delete recipe ingredient "%s"?', objectName),
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
				Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function() { }, function() { });
				Grocy.Api.Delete('objects/recipes_pos/' + objectId, {},
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
		}
	});
});

$(document).on('click', '.recipe-include-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-recipe-include-name');
	var objectId = $(e.currentTarget).attr('data-recipe-include-id');

	bootbox.confirm({
		message: __t('Are you sure to remove included recipe "%s"?', objectName),
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
				Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function() { }, function() { });
				Grocy.Api.Delete('objects/recipes_nestings/' + objectId, {},
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
		}
	});
});

$(document).on('click', '.recipe-pos-show-note-button', function(e)
{
	var note = $(e.currentTarget).attr('data-recipe-pos-note');

	bootbox.alert(note);
});

$(document).on('click', '.recipe-pos-edit-button', function (e)
{
	var recipePosId = $(e.currentTarget).attr('data-recipe-pos-id');

	Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			window.location.href = U('/recipe/' + Grocy.EditObjectId + '/pos/' + recipePosId);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '.recipe-include-edit-button', function (e)
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
	Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			window.location.href = U('/recipe/' + Grocy.EditObjectId + '/pos/new');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
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
				window.location.href = U('/recipe/' + Grocy.EditObjectId);
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
				window.location.href = U('/recipe/' + Grocy.EditObjectId);
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

Grocy.DeleteRecipePictureOnSave = false;
$('#delete-current-recipe-picture-button').on('click', function (e)
{
	Grocy.DeleteRecipePictureOnSave = true;
	$("#current-recipe-picture").addClass("d-none");
	$("#delete-current-recipe-picture-on-save-hint").removeClass("d-none");
	$("#delete-current-recipe-picture-button").addClass("disabled");
});

$('#description').summernote({
	minHeight: '300px',
	lang: __t('summernote_locale')
});

Grocy.Components.UserfieldsForm.Load();
