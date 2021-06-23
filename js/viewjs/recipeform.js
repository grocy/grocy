import { WindowMessageBag } from '../helpers/messagebag';

function recipeformView(Grocy, scope = null)
{
	var $scope = $;
	var top = scope != null ? $(scope) : $(document);
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");
	var recipepicker = Grocy.Use("recipepicker");
	var userfields = Grocy.Use("userfieldsform");

	function saveRecipePicture(result, location, jsonData)
	{
		var recipeId = Grocy.EditObjectId || result.created_object_id;
		userfields.Save(() =>
		{
			if (Object.prototype.hasOwnProperty.call(jsonData, "picture_file_name") && !Grocy.DeleteRecipePictureOnSave)
			{
				Grocy.Api.UploadFile($scope("#recipe-picture")[0].files[0], 'recipepictures', jsonData.picture_file_name,
					(result) =>
					{
						window.location.href = U(location + recipeId);
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
				window.location.href = U(location + recipeId);
			}
		});
	}

	$scope('.save-recipe').on('click', function(e)
	{
		e.preventDefault();

		var jsonData = $scope('#recipe-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("recipe-form");

		if ($scope("#recipe-picture")[0].files.length > 0)
		{
			var someRandomStuff = Math.random().toString(36).substring(2, 100) + Math.random().toString(36).substring(2, 100);
			jsonData.picture_file_name = someRandomStuff + $scope("#recipe-picture")[0].files[0].name;
		}

		const location = $scope(e.currentTarget).attr('data-location') == 'return' ? '/recipes?recipe=' : '/recipe/';

		if (Grocy.EditMode == 'create')
		{
			Grocy.Api.Post('objects/recipes', jsonData,
				(result) => saveRecipePicture(result, location, jsonData));
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
			(result) => saveRecipePicture(result, location, jsonData),
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("recipe-form");
				console.error(xhr);
			}
		);
	});

	var recipesPosTables = $scope('#recipes-pos-table').DataTable({
		'order': [[1, 'asc']],
		"orderFixed": [[4, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 },
			{ 'visible': false, 'targets': 4 }
		].concat($.fn.dataTable.defaults.columnDefs),
		'rowGroup': {
			enable: true,
			dataSrc: 4
		}
	});
	$scope('#recipes-pos-table tbody').removeClass("d-none");
	recipesPosTables.columns.adjust().draw();

	var recipesIncludesTables = $scope('#recipes-includes-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#recipes-includes-table tbody').removeClass("d-none");
	recipesIncludesTables.columns.adjust().draw();

	Grocy.FrontendHelpers.ValidateForm('recipe-form');
	$scope("#name").focus();

	$scope('#recipe-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('recipe-form');
	});

	$scope('#recipe-form input').keydown(function(event)
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
				$scope('#save-recipe-button').click();
			}
		}
	});

	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to delete recipe ingredient "%s"?',
		'.recipe-pos-delete-button',
		'data-recipe-pos-name',
		'data-recipe-pos-id',
		'objects/recipes_pos/',
		() => window.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl)
	);

	Grocy.FrontendHelpers.MakeDeleteConfirmBox(
		'Are you sure to remove the included recipe "%s"?',
		'.recipe-include-delete-button',
		'data-recipe-include-name',
		'data-recipe-include-id',
		'objects/recipes_nesting/',
		() => window.postMessage(WindowMessageBag("IngredientsChanged"), Grocy.BaseUrl)
	);

	top.on('click', '.recipe-pos-show-note-button', function(e)
	{
		var note = $(e.currentTarget).attr('data-recipe-pos-note');

		bootbox.alert(note);
	});

	// TODO: LoadSubView
	top.on('click', '.recipe-pos-edit-button', function(e)
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

	top.on('click', '.recipe-include-edit-button', function(e)
	{
		var id = $(e.currentTarget).attr('data-recipe-include-id');
		var recipeId = $(e.currentTarget).attr('data-recipe-included-recipe-id');
		var recipeServings = $(e.currentTarget).attr('data-recipe-included-recipe-servings');

		Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $scope('#recipe-form').serializeJSON(),
			function(result)
			{
				$scope("#recipe-include-editform-title").text(__t("Edit included recipe"));
				$scope("#recipe-include-form").data("edit-mode", "edit");
				$scope("#recipe-include-form").data("recipe-nesting-id", id);
				recipepicker.SetId(recipeId);
				$scope("#includes_servings").val(recipeServings);
				$scope("#recipe-include-editform-modal").modal("show");
				Grocy.FrontendHelpers.ValidateForm("recipe-include-form");
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	});

	$scope("#recipe-pos-add-button").on("click", function(e)
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

	$scope("#recipe-include-add-button").on("click", function(e)
	{
		Grocy.Api.Put('objects/recipes/' + Grocy.EditObjectId, $scope('#recipe-form').serializeJSON(),
			function(result)
			{
				$scope("#recipe-include-editform-title").text(__t("Add included recipe"));
				$scope("#recipe-include-form").data("edit-mode", "create");
				recipepicker.Clear();
				recipepicker.GetInputElement().focus();
				$scope("#recipe-include-editform-modal").modal("show");
				Grocy.FrontendHelpers.ValidateForm("recipe-include-form");
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	});

	$scope('#save-recipe-include-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		if (document.getElementById("recipe-include-form").checkValidity() === false) //There is at least one validation error
		{
			return false;
		}

		var nestingId = $scope("#recipe-include-form").data("recipe-nesting-id");
		var editMode = $scope("#recipe-include-form").data("edit-mode");

		var jsonData = {};
		jsonData.includes_recipe_id = recipepicker.GetValue();
		jsonData.servings = $scope("#includes_servings").val();
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

	$scope("#recipe-picture").on("change", function(e)
	{
		$scope("#recipe-picture-label").removeClass("d-none");
		$scope("#recipe-picture-label-none").addClass("d-none");
		$scope("#delete-current-recipe-picture-on-save-hint").addClass("d-none");
		$scope("#current-recipe-picture").addClass("d-none");
		Grocy.DeleteRecipePictureOnSave = false;
	});

	Grocy.DeleteRecipePictureOnSave = false;
	$scope("#delete-current-recipe-picture-button").on("click", function(e)
	{
		Grocy.DeleteRecipePictureOnSave = true;
		$scope("#current-recipe-picture").addClass("d-none");
		$scope("#delete-current-recipe-picture-on-save-hint").removeClass("d-none");
		$scope("#recipe-picture-label").addClass("d-none");
		$scope("#recipe-picture-label-none").removeClass("d-none");
	});

	userfields.Load();

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

}

window.recipeformView = recipeformView