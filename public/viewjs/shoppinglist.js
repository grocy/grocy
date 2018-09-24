var shoppingListTable = $('#shoppinglist-table').DataTable({
	'paginate': false,
	'order': [[1, 'asc']],
	"orderFixed": [[3, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'visible': false, 'targets': 3 }
	],
	'language': JSON.parse(L('datatables_localization')),
	'scrollY': false,
	'colReorder': true,
	'stateSave': true,
	'stateSaveParams': function(settings, data)
	{
		data.search.search = "";
	},
	'rowGroup': {
		dataSrc: 3
	}
});

$("#search").on("keyup", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}
	
	shoppingListTable.search(value).draw();
});

$("#status-filter").on("change", function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	// Transfer CSS classes of selected element to dropdown element (for background)
	$(this).attr("class", $("#" + $(this).attr("id") + " option[value='" + value + "']").attr("class") + " form-control");
	
	shoppingListTable.column(4).search(value).draw();
});

$(".status-filter-button").on("click", function()
{
	var value = $(this).data("status-filter");
	$("#status-filter").val(value);
	$("#status-filter").trigger("change");
});

$(document).on('click', '.shoppinglist-delete-button', function (e)
{
	e.preventDefault();
	
	var shoppingListItemId = $(e.currentTarget).attr('data-shoppinglist-id');

	Grocy.Api.Get('delete-object/shopping_list/' + shoppingListItemId,
		function(result)
		{
			$('#shoppinglistitem-' + shoppingListItemId + '-row').fadeOut(500, function()
			{
				$(this).remove();
			});
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '#add-products-below-min-stock-amount', function(e)
{
	Grocy.Api.Get('stock/add-missing-products-to-shoppinglist',
		function(result)
		{
			window.location.href = U('/shoppinglist');
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$(document).on('click', '#clear-shopping-list', function(e)
{
	bootbox.confirm({
		message: L('Are you sure to empty the shopping list?'),
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
				Grocy.Api.Get('stock/clear-shopping-list',
					function(result)
					{
						$('#shoppinglist-table tbody tr').fadeOut(500, function()
						{
							$(this).remove();
						});
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
