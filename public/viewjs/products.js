var userfieldsColumns = userfields.map(function(userfield) {
	if (userfield.show_as_column_in_tables != 1) return null;
	return {
		data: 'userfields.' + userfield.name,
		defaultContent: ''
	};
}).filter(function(userfield) {
	return userfield !== null;
});

var productsTable = $('#products-table').DataTable({
	ajax: function(data, callback, settings) {
		Grocy.FrontendHelpers.BeginUiBusy();

		var query = [];
		if (GetUriParam('only_in_stock')) {
			query.push('only_in_stock=true');
		}
		if (!GetUriParam('include_disabled')) {
			query.push('query%5B%5D=' + encodeURIComponent('active=1'));
		}

		data.columns.forEach(function(column) {
			var search = column.search.value.trim();
			if (search.length > 0) {
				query.push('query%5B%5D=' + encodeURIComponent(column.data + '=' + search));
			}
		});

		var search = data.search.value.trim();
		if (search.length > 0) {
			query.push('search=' + encodeURIComponent(search));
		}

		query.push('limit=' + encodeURIComponent(data.length));
		query.push('offset=' + encodeURIComponent(data.start));
		query.push('order=' + encodeURIComponent(data.order.map(function(order) {
			return data.columns[order.column].data + ':' + order.dir;
		}).join(',')));

		Grocy.Api.Get('objects/products' + (query.length > 0 ? '?' + query.join('&') : ''),
			function(result, meta) {
				callback({
					data: result,
					recordsTotal: meta.recordsTotal,
					recordsFiltered: meta.recordsFiltered,
				});
				Grocy.FrontendHelpers.EndUiBusy();
			},
			function(xhr) {
				Grocy.FrontendHelpers.EndUiBusy();
				Grocy.FrontendHelpers.ShowGenericError('Server error', xhr.response);
			}
		);
	},
	paging: true,
	serverSide: true,
	deferRender: true,
	autoWidth: true,
	order: [
		[1, 'asc']
	],
	columns: [{
			searchable: false,
			orderable: false,
			render: function(data, type, row, meta) {
				return '<a class="btn btn-info btn-sm" href="/product/' + encodeURIComponent(row.id) + '"' +
					'	data-toggle="tooltip" title="' + __t('Edit this item') + '">' +
					'	<i class="fas fa-edit"></i>' +
					'</a>' +
					'<a class="btn btn-danger btn-sm product-delete-button" href="#"' +
					'	data-product-id="' + row.id + '" data-product-name="' + row.name + '"' +
					'	data-toggle="tooltip" title="' + __t('Delete this item') + '">' +
					'	<i class="fas fa-trash"></i>' +
					'</a>' +
					'<div class="dropdown d-inline-block">' +
					'	<button class="btn btn-sm btn-light text-secondary" type="button"' +
					'		data-toggle="dropdown">' +
					'		<i class="fas fa-ellipsis-v"></i>' +
					'	</button>' +
					'	<div class="table-inline-menu dropdown-menu dropdown-menu-right">' +
					'		<a class="dropdown-item" type="button"' +
					'			href="/product/new?copy-of=' + encodeURIComponent(row.id) + '">' +
					'			<span class="dropdown-item-text">' + __t('Copy') + '</span>' +
					'		</a>' +
					'		<a class="dropdown-item merge-products-button"' +
					'			data-product-id="' + row.id + '" data-product-name="' + row.name + '"' +
					'           type="button" href="#" >' +
					'			<span class="dropdown-item-text">' + __t('Merge') + '</span>' +
					'		</a>' +
					'	</div>' +
					'</div>';
			}
		},
		{
			data: 'name',
			searchable: true,
			render: function(data, type, row, meta) {
				return data + (row.picture_file_name ? (
					' <i class="fas fa-image text-muted" data-toggle="tooltip" ' +
					'title="' + __t('This product has a picture') + '"></i>'
				) : '');
			}
		},
		{
			data: 'location.name',
			defaultContent: '',
			visible: Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING
		},
		{
			data: 'min_stock_amount',
			type: 'html-num-fmt',
			render: function(data, type, row, meta) {
				return '<span class="locale-number locale-number-quantity-amount">' + data + '</span>';
			}
		},
		{
			data: 'qu_purchase.name',
			defaultContent: ''
		},
		{
			data: 'qu_stock.name',
			defaultContent: ''
		},
		{
			data: 'product_group.name',
			defaultContent: ''
		},
		{
			data: 'shopping_location.name',
			defaultContent: '',
			visible: Grocy.FeatureFlags.GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING
		}
	].concat(userfieldsColumns),
});

productsTable.on('draw', function() {
	$('[data-toggle=tooltip]').tooltip();
	$('[data-toggle=dropdown]').dropdown();
});

$('#search').on('keyup', Delay(function() {
	var value = $(this).val();
	if (value === 'all') {
		value = '';
	}
	productsTable.search(value).draw();
}, 500));

$('#product-group-filter').on('change', function() {
	var value = $('#product-group-filter option:selected').text();
	if (value === __t('All')) {
		value = '';
	}
	productsTable.column(productsTable.colReorder.transpose(6)).search(value).draw();
});

$('#clear-filter-button').on('click', function() {
	$('#search').val('');
	$('#product-group-filter').val('all');
	productsTable.column(productsTable.colReorder.transpose(6)).search('');
	productsTable.search('');
	if ($('#show-disabled').is(':checked') || $('#show-only-in-stock').is(':checked')) {
		$('#show-disabled').prop('checked', false);
		$('#show-only-in-stock').prop('checked', false);
		RemoveUriParam('include_disabled');
		RemoveUriParam('only_in_stock');
	}
	productsTable.draw();
});

if (typeof GetUriParam('product-group') !== 'undefined') {
	$('#product-group-filter').val(GetUriParam('product-group'));
	$('#product-group-filter').trigger('change');
}

$(document).on('click', '.product-delete-button', function(e) {
	var objectName = $(e.currentTarget).attr('data-product-name');
	var objectId = $(e.currentTarget).attr('data-product-id');

	bootbox.confirm({
		message: __t('Are you sure to delete product "%s"?', objectName) + '<br><br>' + __t('This also removes any stock amount, the journal and all other references of this product - consider disabling it instead, if you want to keep that and just hide the product.'),
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
		callback: function(result) {
			if (result === true) {
				jsonData = {};
				jsonData.active = 0;
				Grocy.Api.Delete('objects/products/' + objectId, {},
					function(result) {
						window.location.href = U('/products');
					},
					function(xhr) {
						console.error(xhr);
					}
				);
			}
		}
	});
});

$('#show-disabled').on('change', function() {
	if (this.checked) {
		UpdateUriParam('include_disabled', 'true');
	} else {
		RemoveUriParam('include_disabled');
	}
	productsTable.draw();
});

$('#show-only-in-stock').on('change', function() {
	if (this.checked) {
		UpdateUriParam('only_in_stock', 'true');
	} else {
		RemoveUriParam('only_in_stock');
	}
	productsTable.draw();
});

if (GetUriParam('include_disabled')) {
	$('#show-disabled').prop('checked', true);
}

$(document).on('click', '.merge-products-button', function(e) {
	var $button = $(e.currentTarget);
	var $mergeKeep = $('#merge-products-keep');

	var optionId = $button.attr('data-product-id');
	var optionText = $button.attr('data-product-name');

	if ($mergeKeep.find('option[value="' + optionId + '"]').length) {
		$mergeKeep.val(optionId).trigger('change');
	} else {
		var option = new Option(optionText, optionId, true, true);
		$mergeKeep.append(option).trigger('change');
	}

	$('#merge-products-remove').val(null).trigger('change');
	$('#merge-products-modal').modal('show');
});

$('#merge-products-save-button').on('click', function() {
	var productIdToKeep = $('#merge-products-keep').val();
	var productIdToRemove = $('#merge-products-remove').val();

	Grocy.Api.Post('stock/products/' + productIdToKeep.toString() + '/merge/' + productIdToRemove.toString(), {},
		function(result) {
			window.location.href = U('/products');
		},
		function(xhr) {
			Grocy.FrontendHelpers.ShowGenericError('Error while merging', xhr.response);
		}
	);
});

$('#merge-products-keep, #merge-products-remove').select2({
	dropdownParent: $('#merge-products-modal'),
	ajax: {
		delay: 150,
		transport: function(params, success, failure) {
			var results_per_page = 10;
			var page = params.data.page || 1;
			var term = params.data.term || "";

			var query = [];
			query.push('query%5B%5D=active%3D1');
			query.push('limit=' + encodeURIComponent(results_per_page));
			query.push('offset=' + encodeURIComponent((page - 1) * results_per_page));
			query.push('order=name%3Acollate%20nocase');
			if (term.length > 0) {
				query.push('search=' + encodeURIComponent(term));
			}

			Grocy.Api.Get('objects/products' + (query.length > 0 ? '?' + query.join('&') : ''),
				function(results, meta) {
					success({
						results: results.map(function(result) {
							return {
								id: result.id,
								text: result.name
							};
						}),
						pagination: {
							more: page * results_per_page < meta.recordsFiltered
						}
					});
				},
				function(xhr) {
					failure();
				}
			);
		}
	}
});
