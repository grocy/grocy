$('#save-recipe-button').on('click', function(e)
{
	e.preventDefault();

	Grocy.Api.Post('edit-object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			window.location.href = U('/recipes');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

var recipesPosTables = $('#recipes-pos-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 }
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
	}
});

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
				Grocy.Api.Post('edit-object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function() { }, function() { });
				Grocy.Api.Get('delete-object/recipes_pos/' + objectId,
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

$(document).on('click', '.recipe-inlcude-delete-button', function(e)
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
				Grocy.Api.Post('edit-object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function() { }, function() { });
				Grocy.Api.Get('delete-object/recipes_nestings/' + objectId,
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

	Grocy.Api.Post('add-object/shopping_list', jsonData,
		function(result)
		{
			Grocy.Api.Post('edit-object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(), function () { }, function () { });
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

	Grocy.Api.Post('edit-object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
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

	Grocy.Api.Post('edit-object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			window.location.href = U('/recipe/' + Grocy.EditObjectId + '/included_recipe/' + id);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$("#recipe-pos-add-button").on("click", function(e)
{
	Grocy.Api.Post('edit-object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
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
	Grocy.Api.Post('edit-object/recipes/' + Grocy.EditObjectId, $('#recipe-form').serializeJSON(),
		function(result)
		{
			window.location.href = U('/recipe/' + Grocy.EditObjectId + '/included_recipe/new');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$('#description').summernote({
	minHeight: '300px',
	lang: L('summernote_locale')
});
