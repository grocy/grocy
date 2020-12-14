@extends('layout.default')

@section('title', $__t('Shopping list'))

@section('viewJsName', 'shoppinglistprint')

@section('content')
<div>
	<h1 class="text-center">
		<img src="{{ $U('/img/grocy_logo.svg?v=', true) }}{{ $version }}"
			height="30"
			class="d-print-flex mx-auto">
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
			@include('components.shoppinglisttable', array(
			'listItems' => $listItems,
			'products' => $products,
			'quantityunits' => $quantityunits,
			'missingProducts' => $missingProducts,
			'productGroups' => $productGroups,
			'selectedShoppingListId' => $selectedShoppingList->id,
			'quantityUnitConversionsResolved' => $quantityUnitConversionsResolved,
			'productUserfields' => $productUserfields,
			'productUserfieldValues' => $productUserfieldValues,
			'userfields' => $userfields,
			'userfieldValues' => $userfieldValues,
			'isPrint' => true
			))
		</div>
	</div>
	<div class="row w-75">
		<div class="col">
			<h5>{{ $__t('Notes') }}</h5>
			<p>{!! FindObjectInArrayByPropertyValue($shoppingLists, 'id', $selectedShoppingListId)->description !!}</p>
		</div>
	</div>
</div>

@stop