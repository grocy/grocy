var productsTable = $('#products-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	]
});
$('#products-table tbody').removeClass("d-none");
productsTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	productsTable.search(value).draw();
}, 200));

$("#product-group-filter").on("change", function()
{
	var value = $("#product-group-filter option:selected").text();
	if (value === __t("All"))
	{
		value = "";
	}

	productsTable.column(7).search(value).draw();
});

if (typeof GetUriParam("product-group") !== "undefined")
{
	$("#product-group-filter").val(GetUriParam("product-group"));
	$("#product-group-filter").trigger("change");
}

$(document).on('click', '.product-delete-button', function (e)
{
	var objectName = $(e.currentTarget).attr('data-product-name');
	var objectId = $(e.currentTarget).attr('data-product-id');

	Grocy.Api.Get('stock/products/' + objectId,
		function(productDetails)
		{
			var stockAmount = productDetails.stock_amount || '0';

			if (stockAmount.toString() == "0")
			{
				bootbox.confirm({
					message: __t('Are you sure to delete product "%s"?', objectName),
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
					callback: function (result)
					{
						if (result === true)
						{
							Grocy.Api.Delete('objects/products/' + objectId, {},
								function (result)
								{
									window.location.href = U('/products');
								},
								function (xhr)
								{
									console.error(xhr);
								}
							);
						}
					}
				});
			}
			else
			{
				bootbox.alert({
					title: __t('Delete not possible'),
					message: __t('This product cannot be deleted because it is in stock, please remove the stock amount first.') + '<br><br>' + __t('Stock amount') + ': ' + stockAmount + ' ' + __n(stockAmount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural),
					closeButton: false
				});
			}
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});
