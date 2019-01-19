$('#save-recipe-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#recipe-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("recipe-form");

	Grocy.Api.Put('object/recipes/' + Grocy.EditObjectId, jsonData,
		function(result)
		{
			window.location.href = U('/recipes');
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
	'language': JSON.parse(L('datatables_localization')),
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

var recipesIncludesTables = $('#recipes-includes-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
	],
	'language': JSON.parse(L('datatables_localization')),
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

Grocy.FrontendHelpers.ValidateForm('recipe-form');
$("#name").focus();

$('#recipe-form input').keyup(function (event)
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
		message: L('Are you sure to delete recipe ingredient "#1"?', objectName),
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
				Grocy.Api.Put('object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function() { }, function() { });
				Grocy.Api.Delete('object/recipes_pos/' + objectId,
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
		message: L('Are you sure to remove included recipe "#1"?', objectName),
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
				Grocy.Api.Put('object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function() { }, function() { });
				Grocy.Api.Delete('object/recipes_nestings/' + objectId,
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

$(document).on('click', '.recipe-pos-order-missing-button', function(e)
{
	var productName = $(e.currentTarget).attr('data-product-name');
	var productId = $(e.currentTarget).attr('data-product-id');
	var productAmount = $(e.currentTarget).attr('data-product-amount');
	var recipeName = $(e.currentTarget).attr('data-recipe-name');

	var jsonData = {};
	jsonData.product_id = productId;
	jsonData.amount = productAmount;
	jsonData.note = L('Added for recipe #1', recipeName);

	Grocy.Api.Post('object/shopping_list', jsonData,
		function(result)
		{
			Grocy.Api.Put('object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function () { }, function () { });
			window.location.href = U('/recipe/' + Grocy.EditObjectId);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '.recipe-pos-show-note-button', function(e)
{
	var note = $(e.currentTarget).attr('data-recipe-pos-note');

	bootbox.alert(note);
});

$(document).on('click', '.recipe-pos-edit-button', function (e)
{
	var recipePosId = $(e.currentTarget).attr('data-recipe-pos-id');

	Grocy.Api.Put('object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
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
	console.log(recipeId);
	Grocy.Api.Put('object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			$("#recipe-include-editform-title").text(L("Edit included recipe"));
			$("#recipe-include-form").data("edit-mode", "edit");
			$("#recipe-include-form").data("recipe-nesting-id", id);
			$("#includes_recipe_id").val(recipeId);
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
	Grocy.Api.Put('object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
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
	Grocy.Api.Put('object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			$("#recipe-include-editform-title").text(L("Add included recipe"));
			$("#recipe-include-form").data("edit-mode", "create");
			$("#includes_recipe_id").val("");
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

	var nestingId = $("#recipe-include-form").data("recipe-nesting-id");
	var editMode = $("#recipe-include-form").data("edit-mode");

	var jsonData = $('#recipe-include-form').serializeJSON();
	jsonData.recipe_id = Grocy.EditObjectId;

	if (editMode === 'create')
	{
		Grocy.Api.Post('object/recipes_nestings', jsonData,
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
		Grocy.Api.Put('object/recipes_nestings/' + nestingId, jsonData,
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

$('#description').summernote({
	minHeight: '300px',
	lang: L('summernote_locale')
});
