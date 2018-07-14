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
	'stateSave': true
});

$("#search").on("keyup", function ()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	recipesPosTables.search(value).draw();
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
		if (document.getElementById('recipe-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
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
			window.location.href = U('/recipe/' + Grocy.EditObjectId);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
