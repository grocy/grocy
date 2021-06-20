var productsTable = $('#products-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 },
		{ 'visible': false, 'targets': 7 },
		{ "type": "html-num-fmt", "targets": 3 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#products-table tbody').removeClass("d-none");

Grocy.FrontendHelpers.InitDataTable(productsTable, null, function()
{
	$("#search").val("");
	productsTable.search("").draw();
	$("#show-disabled").prop('checked', false);
})

Grocy.FrontendHelpers.MakeFilterForColumn("#product-group-filter", 6, productsTable);
if (typeof GetUriParam("product-group") !== "undefined")
{
	$("#product-group-filter").val(GetUriParam("product-group"));
	$("#product-group-filter").trigger("change");
}

Grocy.FrontendHelpers.MakeDeleteConfirmBox(
	(objectId, objectName) =>
	{
		return __t('Are you sure to delete product "%s"?', objectName) +
			'<br><br>' +
			__t('This also removes any stock amount, the journal and all other references of this product - consider disabling it instead, if you want to keep that and just hide the product.');
	},
	'.product-delete-button',
	'data-product-name',
	'data-product-id',
	'objects/products/',
	'/products'
);

$("#show-disabled").change(function()
{
	if (this.checked)
	{
		window.location.href = U('/products?include_disabled');
	}
	else
	{
		window.location.href = U('/products');
	}
});

if (GetUriParam('include_disabled'))
{
	$("#show-disabled").prop('checked', true);
}


$(".merge-products-button").on("click", function(e)
{
	var productId = $(e.currentTarget).attr("data-product-id");
	$("#merge-products-keep").val(productId);
	$("#merge-products-remove").val("");
	$("#merge-products-modal").modal("show");
});

$("#merge-products-save-button").on("click", function()
{
	var productIdToKeep = $("#merge-products-keep").val();
	var productIdToRemove = $("#merge-products-remove").val();

	Grocy.Api.Post("stock/products/" + productIdToKeep.toString() + "/merge/" + productIdToRemove.toString(), {},
		function(result)
		{
			window.location.href = U('/products');
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while merging products', xhr.response);
		}
	);
});
