@extends('layout.default')

@section('title', $__t('Shopping list'))
@section('activeNav', 'shoppinglist')
@section('viewJsName', 'shoppinglist')

@push('pageScripts')
	<script src="{{ $U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
	<script src="{{ $U('/viewjs/purchase.js?v=', true) }}{{ $version }}"></script>
@endpush

@push('pageStyles')
	<link href="{{ $U('/node_modules/animate.css/animate.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
	<link href="{{ $U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}" rel="stylesheet">

	<style>
	tr.dtrg-group {
		cursor: pointer;
	}
	</style>
@endpush

@section('content')
@if(GROCY_FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS)
<div class="row border-bottom pb-2 mb-2 d-print-none hide-on-fullscreen-card">
	<div class="col-xs-12 col-md-4">
		<label for="selected-shopping-list">{{ $__t('Selected shopping list') }}</label>
		<select class="form-control" id="selected-shopping-list">
			@foreach($shoppingLists as $shoppingList)
			<option @if($shoppingList->id == $selectedShoppingListId) selected="selected" @endif value="{{ $shoppingList->id }}">{{ $shoppingList->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="col-xs-12 col-md-8">
		<label for="selected-shopping-list">&nbsp;</label><br>
		<a class="btn btn-outline-dark responsive-button" href="{{ $U('/shoppinglist/new') }}">
			<i class="fas fa-plus"></i> {{ $__t('New shopping list') }}
		</a>
		<a id="delete-selected-shopping-list" class="btn btn-outline-danger responsive-button @if($selectedShoppingListId == 1) disabled @endif" href="#">
			<i class="fas fa-trash"></i> {{ $__t('Delete shopping list') }}
		</a>
		<a id="print-shopping-list-button" class="btn btn-outline-dark responsive-button" href="#">
			<i class="fas fa-print"></i> {{ $__t('Print') }}
		</a>
		<a id="shopping-list-compact-view-button" class="btn btn-outline-dark responsive-button switch-view-mode-button" href="#">
			<i class="fas fa-compress-arrows-alt"></i> {{ $__t('Compact view') }}
		</a>
		<!--<div class="dropdown d-inline-block">
			<button class="btn btn-outline-dark responsive-button dropdown-toggle" data-toggle="dropdown"><i class="fas fa-file-export"></i> {{ $__t('Output') }}</button>
			<div class="dropdown-menu">
				<a id="print-shopping-list-button" class="dropdown-item" href="#"><i class="fas fa-print"></i> {{ $__t('Print') }}</a>
			</div>
		</div>-->
	</div>
</div>
@else
<input type="hidden" name="selected-shopping-list" id="selected-shopping-list" value="1">
@endif

<div class="row d-print-none hide-on-fullscreen-card">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark responsive-button" href="{{ $U('/shoppinglistitem/new?list=' . $selectedShoppingListId) }}">
				<i class="fas fa-plus"></i> {{ $__t('Add item') }}
			</a>
			<a id="clear-shopping-list" class="btn btn-outline-danger responsive-button @if($listItems->count() == 0) disabled @endif" href="#">
				<i class="fas fa-trash"></i> {{ $__t('Clear list') }}
			</a>
			<a id="add-products-below-min-stock-amount" class="btn btn-outline-primary responsive-button" href="#">
				<i class="fas fa-cart-plus"></i> {{ $__t('Add products that are below defined min. stock amount') }}
			</a>
			<a id="add-all-items-to-stock-button" class="btn btn-outline-primary responsive-button" href="#">
				<i class="fas fa-box"></i> {{ $__t('Add all list items to stock') }}
			</a>
			@if(!GROCY_FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS)
			<a id="shopping-list-compact-view-button" class="btn btn-outline-dark responsive-button switch-view-mode-button" href="#">
				<i class="fas fa-compress-arrows-alt"></i> {{ $__t('Compact view') }}
			</a>
			@endif
		</h1>
		<p data-status-filter="belowminstockamount" class="btn btn-lg btn-info status-filter-button responsive-button">{{ $__n(count($missingProducts), '%s product is below defined min. stock amount', '%s products are below defined min. stock amount') }}</p>
	</div>
</div>

<div class="row mt-3 d-print-none hide-on-fullscreen-card">
	<div class="col-xs-12 col-md-4">
		<label for="search">{{ $__t('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
	<div class="col-xs-12 col-md-4">
		<label for="status-filter">{{ $__t('Filter by status') }}</label> <i class="fas fa-filter"></i>
		<select class="form-control" id="status-filter">
			<option class="bg-white" value="all">{{ $__t('All') }}</option>
			<option class="bg-info" value="belowminstockamount">{{ $__t('Below min. stock amount') }}</option>
			<option class="bg-white" value="xxUNDONExx">{{ $__t('Only undone items') }}</option>
		</select>
	</div>
</div>

<div id="shoppinglist-main" class="row d-print-none">
	<div class="@if(boolval($userSettings['shopping_list_show_calendar'])) col-xs-12 col-md-8 @else col-12 @endif pb-3">
		<a id="shopping-list-normal-view-button" class="btn btn-outline-dark btn-block switch-view-mode-button d-none" href="#">
			<i class="fas fa-expand-arrows-alt"></i> {{ $__t('Normal view') }}
		</a>
		<table id="shoppinglist-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Product') }} / <em>{{ $__t('Note') }}</em></th>
					<th>{{ $__t('Amount') }}</th>
					<th class="d-none">Hiden product group</th>
					<th class="d-none">Hidden status</th>

					@include('components.userfields_thead', array(
						'userfields' => $userfields
					))

				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($listItems as $listItem)
				<tr id="shoppinglistitem-{{ $listItem->id }}-row" class="@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) table-info @endif @if($listItem->done == 1) text-muted text-strike-through @endif">
					<td class="fit-content border-right">
						<a class="btn btn-success btn-sm order-listitem-button" href="#"
							data-item-id="{{ $listItem->id }}"
							data-item-done="{{ $listItem->done }}"
							data-toggle="tooltip" data-placement="right" title="{{ $__t('Mark this item as done') }}">
							<i class="fas fa-check"></i>
						</a>
						<a class="btn btn-sm btn-info" href="{{ $U('/shoppinglistitem/') . $listItem->id . '?list=' . $selectedShoppingListId }}"
							data-toggle="tooltip" data-placement="right" title="{{ $__t('Edit this item') }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-sm btn-danger shoppinglist-delete-button" href="#" data-shoppinglist-id="{{ $listItem->id }}"
							data-toggle="tooltip" data-placement="right" title="{{ $__t('Delete this item') }}">
							<i class="fas fa-trash"></i>
						</a>
						<a class="btn btn-sm btn-primary @if(empty($listItem->product_id)) disabled @else shopping-list-stock-add-workflow-list-item-button @endif" href="{{ $U('/purchase?embedded&flow=shoppinglistitemtostock&product=') }}{{ $listItem->product_id }}&amount={{ $listItem->amount }}&listitemid={{ $listItem->id }}" @if(!empty($listItem->product_id)) data-toggle="tooltip" title="{{ $__t('Add %1$s of %2$s to stock', $listItem->amount . ' ' . $__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural), FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name, $listItem->amount) }}" @endif>
							<i class="fas fa-box"></i>
						</a>
					</td>
					<td>
						@if(!empty($listItem->product_id)) {{ FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name }}<br>@endif<em>{!! nl2br($listItem->note) !!}</em>
					</td>
					<td>
						<span class="locale-number locale-number-quantity-amount">{{ $listItem->amount }}</span> @if(!empty($listItem->product_id)){{ $__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural) }}@endif
					</td>
					<td class="d-none">
						@if(!empty(FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)) {{ FindObjectInArrayByPropertyValue($productGroups, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)->name }} @else <span class="font-italic font-weight-light">{{ $__t('Ungrouped') }}</span> @endif
					</td>
					<td id="shoppinglistitem-{{ $listItem->id }}-status-info" class="d-none">
						@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) belowminstockamount @endif
						@if($listItem->done != 1) xxUNDONExx @endif
					</td>

					@include('components.userfields_tbody', array(
						'userfields' => $userfields,
						'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $listItem->product_id)
					))

				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	@if(boolval($userSettings['shopping_list_show_calendar']))
	<div class="col-xs-12 col-md-4 mt-md-2 d-print-none">
		@include('components.calendarcard')
	</div>
	@endif

	<div class="@if(boolval($userSettings['shopping_list_show_calendar'])) col-xs-12 col-md-8 @else col-12 @endif d-print-none pt-2">
		<div class="form-group">
			<label class="text-larger font-weight-bold" for="notes">{{ $__t('Notes') }}</label>
			<a id="save-description-button" class="btn btn-success btn-sm ml-1 mb-2" href="#">{{ $__t('Save') }}</a>
			<a id="clear-description-button" class="btn btn-danger btn-sm ml-1 mb-2" href="#">{{ $__t('Clear') }}</a>
			<textarea class="form-control wysiwyg-editor" id="description" name="description">{{ FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->description }}</textarea>
		</div>
	</div>
</div>

<div class="modal fade" id="shopping-list-stock-add-workflow-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content text-center">
			<div class="modal-body">
				<iframe id="shopping-list-stock-add-workflow-purchase-form-frame" class="embed-responsive" src=""></iframe>
			</div>
			<div class="modal-footer">
				<span id="shopping-list-stock-add-workflow-purchase-item-count" class="d-none mr-auto"></span>
				<button id="shopping-list-stock-add-workflow-skip-button" type="button" class="btn btn-primary"><i class="fas fa-angle-double-right"></i> {{ $__t('Skip') }}</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Close') }}</button>
			</div>
		</div>
	</div>
</div>

<div class="d-none d-print-block">
	<h1 class="text-center">
		<img src="{{ $U('/img/grocy_logo.svg?v=', true) }}{{ $version }}" height="30" class="d-print-flex mx-auto">
		{{ $__t("Shopping list") }}
	</h1>
	@if (FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->name != $__t("Shopping list"))
	<h3 class="text-center">
		{{ FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->name }}
	</h3>
	@endif
	<h6 class="text-center mb-4">
		{{ $__t('Time of printing') }}:
		<span class="d-inline print-timestamp"></span>
	</h6>
	<div class="row w-75">
		<div class="col">
			<table class="table">
				<thead>
					<tr>
						<th>{{ $__t('Product') }} / <em>{{ $__t('Note') }}</em></th>
						<th>{{ $__t('Amount') }}</th>

						@include('components.userfields_thead', array(
							'userfields' => $userfields
						))

					</tr>
				</thead>
				<tbody>
					@foreach($listItems as $listItem)
					<tr>
						<td>
							@if(!empty($listItem->product_id)) {{ FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name }}<br>@endif<em>{!! nl2br($listItem->note) !!}</em>
						</td>
						<td>
							{{ $listItem->amount }} @if(!empty($listItem->product_id)){{ $__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->qu_id_purchase)->name_plural) }}@endif
						</td>

						@include('components.userfields_tbody', array(
							'userfields' => $userfields,
							'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $listItem->product_id)
						))

					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
	<div class="row w-75">
		<div class="col">
			<h5>{{ $__t('Notes') }}</h5>
			<p id="description-for-print"></p>
		</div>
	</div>
</div>
@stop
