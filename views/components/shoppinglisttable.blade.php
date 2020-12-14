@php if(empty($listItems)) { $listItems = []; } @endphp
@php if(empty($products)) { $products = []; } @endphp
@php if(empty($quantityunits)) { $quantityunits = []; } @endphp
@php if(empty($missingProducts)) { $missingProducts = []; } @endphp
@php if(empty($productGroups)) { $productGroups = []; } @endphp
@php if(!isset($selectedShoppingListId)) { $selectedShoppingListId = 1; } @endphp
@php if(empty($quantityUnitConversionsResolved)) { $quantityUnitConversionsResolved = []; } @endphp
@php if(empty($productUserfields)) { $productUserfields = []; } @endphp
@php if(empty($productUserfieldValues)) { $productUserfieldValues = []; } @endphp
@php if(empty($userfields)) { $userfields = []; } @endphp
@php if(empty($userfieldValues)) { $userfieldValues = []; } @endphp
@php if(!isset($isPrint)) { $isPrint = false; } @endphp
@php $tableId = ($isPrint ? "shoppinglist-table-print" : "shoppinglist-table"); @endphp

@push('componentStyles')
<link href="{{ $U('/node_modules/datatables.net-rowgroup-bs4/css/rowGroup.bootstrap4.min.css?v=', true) }}{{ $version }}"
	rel="stylesheet">
<style>
	tr.dtrg-group {
		cursor: pointer;
	}
</style>
@endpush

@push('componentScripts')
<script src="{{ $U('/node_modules/datatables.net-rowgroup/js/dataTables.rowGroup.min.js?v=', true) }}{{ $version }}"></script>
<script src="{{ $U('/node_modules/datatables.net-rowgroup-bs4/js/rowGroup.bootstrap4.min.js?v=', true) }}{{ $version }}"></script>
<script>
	var tableId = "#"+ "<?php echo $tableId ?>";
</script>
<script src="{{ $U('/viewjs/components/shoppinglisttable.js', true) }}?v={{ $version }}"></script>
@endpush

<table id="{{ $tableId }}"
	class="table table-sm table-striped nowrap  @if($isPrint) w-75 @else w-100 @endif">
	<thead>
		<tr>
			<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
					data-toggle="tooltip"
					data-toggle="tooltip"
					title="{{ $__t('Hide/view columns') }}"
					data-table-selector="#{{ $tableId }}"
					href="#"><i class="fas fa-eye"></i></a>
			</th>
			<th class="d-none">Hidden product group</th>
			<th class="d-none">Hidden status</th>
			<th>{{ $__t('Product') }} / <em>{{ $__t('Note') }}</em></th>
			<th>{{ $__t('Amount') }}</th>

			@include('components.userfields_thead', array(
			'userfields' => $userfields
			))
			@include('components.userfields_thead', array(
			'userfields' => $productUserfields
			))

		</tr>
	</thead>
	<tbody class="d-none">
		@foreach($listItems as $listItem)
		<tr id="shoppinglistitem-{{ $listItem->id }}-row"
			class="@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) table-info @endif @if($listItem->done == 1) text-muted text-strike-through @endif">

			<td class="fit-content border-right">
				@if(!$isPrint)
				<a class="btn btn-success btn-sm order-listitem-button"
					href="#"
					data-item-id="{{ $listItem->id }}"
					data-item-done="{{ $listItem->done }}"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Mark this item as done') }}">
					<i class="fas fa-check"></i>
				</a>
				<a class="btn btn-sm btn-info show-as-dialog-link"
					href="{{ $U('/shoppinglistitem/' . $listItem->id . '?embedded&list=' . $selectedShoppingListId ) }}"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Edit this item') }}">
					<i class="fas fa-edit"></i>
				</a>
				<a class="btn btn-sm btn-danger shoppinglist-delete-button"
					href="#"
					data-shoppinglist-id="{{ $listItem->id }}"
					data-toggle="tooltip"
					data-placement="right"
					title="{{ $__t('Delete this item') }}">
					<i class="fas fa-trash"></i>
				</a>
				<a class="btn btn-sm btn-primary @if(!GROCY_FEATURE_FLAG_STOCK) d-none @endif @if(empty($listItem->product_id)) disabled @else shopping-list-stock-add-workflow-list-item-button @endif"
					href="{{ $U('/purchase?embedded&flow=shoppinglistitemtostock&product=') }}{{ $listItem->product_id }}&amount={{ $listItem->amount }}&listitemid={{ $listItem->id }}&quId={{ $listItem->qu_id }}"
					@if(!empty($listItem->product_id)) data-toggle="tooltip" title="{{ $__t('Add %1$s of %2$s to stock', $listItem->amount . ' ' . $__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $listItem->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $listItem->qu_id)->name_plural), FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name, $listItem->amount) }}" @endif>
					<i class="fas fa-box"></i>
				</a>
				@endif
			</td>

			<td class="d-none">
				@if(!empty(FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)) {{ FindObjectInArrayByPropertyValue($productGroups, 'id', FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->product_group_id)->name }} @else <span class="font-italic font-weight-light">{{ $__t('Ungrouped') }}</span> @endif
			</td>
			<td id="shoppinglistitem-{{ $listItem->id }}-status-info"
				class="d-none">
				@if(FindObjectInArrayByPropertyValue($missingProducts, 'id', $listItem->product_id) !== null) belowminstockamount @endif
				@if($listItem->done != 1) xxUNDONExx @endif
			</td>

			<td class="product-name-cell cursor-link"
				data-product-id="{{ $listItem->product_id }}">
				@if(!empty($listItem->product_id)) {{ FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id)->name }}<br>@endif<em>{!! nl2br($listItem->note) !!}</em>
			</td>
			<td>
				@if(!empty($listItem->product_id))
				@php
				$listItem->amount_origin_qu = $listItem->amount;
				$product = FindObjectInArrayByPropertyValue($products, 'id', $listItem->product_id);
				$productQuConversions = FindAllObjectsInArrayByPropertyValue($quantityUnitConversionsResolved, 'product_id', $product->id);
				$productQuConversions = FindAllObjectsInArrayByPropertyValue($productQuConversions, 'from_qu_id', $product->qu_id_stock);
				$productQuConversion = FindObjectInArrayByPropertyValue($productQuConversions, 'to_qu_id', $listItem->qu_id);
				if ($productQuConversion)
				{
				$listItem->amount = $listItem->amount * $productQuConversion->factor;
				}
				@endphp
				@endif
				<span class="locale-number locale-number-quantity-amount">{{ $listItem->amount }}</span> @if(!empty($listItem->product_id)){{ $__n($listItem->amount, FindObjectInArrayByPropertyValue($quantityunits, 'id', $listItem->qu_id)->name, FindObjectInArrayByPropertyValue($quantityunits, 'id', $listItem->qu_id)->name_plural) }}@endif
			</td>


			@include('components.userfields_tbody', array(
			'userfields' => $userfields,
			'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $listItem->id)
			))
			@include('components.userfields_tbody', array(
			'userfields' => $productUserfields,
			'userfieldValues' => FindAllObjectsInArrayByPropertyValue($userfieldValues, 'object_id', $listItem->product_id)
			))

		</tr>
		@endforeach
	</tbody>
</table>