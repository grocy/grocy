function productsView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var productsTable = $scope('#products-table').DataTable({
		'order': [[1, 'asc']],
		'columnDefs': [
			{ 'orderable': false, 'targets': 0 },
			{ 'searchable': false, "targets": 0 },
			{ 'visible': false, 'targets': 7 },
			{ "type": "html-num-fmt", "targets": 3 }
		].concat($.fn.dataTable.defaults.columnDefs)
	});
	$scope('#products-table tbody').removeClass("d-none");

	Grocy.FrontendHelpers.InitDataTable(productsTable, null, function()
	{
		$scope("#search").val("");
		productsTable.search("").draw();
		$scope("#show-disabled").prop('checked', false);
	})

	Grocy.FrontendHelpers.MakeFilterForColumn("#product-group-filter", 6, productsTable);
	if (typeof Grocy.GetUriParam("product-group") !== "undefined")
	{
		$scope("#product-group-filter").val(Grocy.GetUriParam("product-group"));
		$scope("#product-group-filter").trigger("change");
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

	$scope("#show-disabled").change(function()
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

	if (Grocy.GetUriParam('include_disabled'))
	{
		$scope("#show-disabled").prop('checked', true);
	}


	$scope(".merge-products-button").on("click", function(e)
	{
		var productId = $scope(e.currentTarget).attr("data-product-id");
		$scope("#merge-products-keep").val(productId);
		$scope("#merge-products-remove").val("");
		$scope("#merge-products-modal").modal("show");
	});

	$scope("#merge-products-save-button").on("click", function()
	{
		var productIdToKeep = $scope("#merge-products-keep").val();
		var productIdToRemove = $scope("#merge-products-remove").val();

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

}


window.productsView = productsView
