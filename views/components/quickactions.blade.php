@if(boolval($userSettings['use_alternative_actions_on_stock_overview_page']))
<a id="product-{{ $currentStockEntry->product_id }}-consume-button"
	class="permission-STOCK_CONSUME btn btn-success btn-sm product-consume-button show-as-dialog-link @if($currentStockEntry->amount_aggregated == 0) disabled @endif"
	href="{{ $U('/consume?embedded&product=' . $currentStockEntry->product_id ) }}">
	<i class="fa-solid fa-utensils"></i> {{ $__t('Consume') }}
</a>
<a id="product-{{ $currentStockEntry->product_id }}-consume-button"
	class="permission-STOCK_TRANSFER btn btn-info btn-sm show-as-dialog-link @if($currentStockEntry->amount <= 0) disabled @endif"
	href="{{ $U('/transfer?embedded&product=' . $currentStockEntry->product_id ) }}">
	<i class="fa-solid fa-exchange-alt"></i>
</a>
@else
<a class="permission-STOCK_CONSUME btn btn-success btn-sm product-consume-button @if($currentStockEntry->amount_aggregated < $currentStockEntry->quick_consume_amount || $currentStockEntry->enable_tare_weight_handling == 1) disabled @endif"
	href="#"
	data-toggle="tooltip"
	data-placement="left"
	title="{{ $__t('Consume %1$s of %2$s', $currentStockEntry->quick_consume_amount_qu_consume . ' ' . $currentStockEntry->qu_consume_name, $currentStockEntry->product_name) }}"
	data-product-id="{{ $currentStockEntry->product_id }}"
	data-product-name="{{ $currentStockEntry->product_name }}"
	data-product-qu-name="{{ $currentStockEntry->qu_stock_name }}"
	data-consume-amount="{{ $currentStockEntry->quick_consume_amount }}">
	<i class="fa-solid fa-utensils"></i> <span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->quick_consume_amount_qu_consume }}</span>
</a>
<a id="product-{{ $currentStockEntry->product_id }}-consume-all-button"
	class="permission-STOCK_CONSUME btn btn-danger btn-sm product-consume-button @if($currentStockEntry->amount_aggregated == 0) disabled @endif"
	href="#"
	data-toggle="tooltip"
	data-placement="right"
	title="{{ $__t('Consume all %s which are currently in stock', $currentStockEntry->product_name) }}"
	data-product-id="{{ $currentStockEntry->product_id }}"
	data-product-name="{{ $currentStockEntry->product_name }}"
	data-product-qu-name="{{ $currentStockEntry->qu_stock_name }}"
	data-consume-amount="@if($currentStockEntry->enable_tare_weight_handling == 1){{$currentStockEntry->tare_weight}}@else{{$currentStockEntry->amount}}@endif"
	data-original-total-stock-amount="{{$currentStockEntry->amount}}">
	<i class="fa-solid fa-utensils"></i> {{ $__t('All') }}
</a>
@if(GROCY_FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING)
<a class="btn btn-success btn-sm product-open-button @if($currentStockEntry->amount_aggregated < $currentStockEntry->quick_open_amount || $currentStockEntry->amount_aggregated == $currentStockEntry->amount_opened_aggregated || $currentStockEntry->enable_tare_weight_handling == 1) disabled @endif"
	href="#"
	data-toggle="tooltip"
	data-placement="left"
	title="{{ $__t('Mark %1$s of %2$s as open', $currentStockEntry->quick_open_amount_qu_consume . ' ' . $currentStockEntry->qu_consume_name, $currentStockEntry->product_name) }}"
	data-product-id="{{ $currentStockEntry->product_id }}"
	data-product-name="{{ $currentStockEntry->product_name }}"
	data-product-qu-name="{{ $currentStockEntry->qu_stock_name }}"
	data-open-amount="{{ $currentStockEntry->quick_open_amount }}">
	<i class="fa-solid fa-box-open"></i> <span class="locale-number locale-number-quantity-amount">{{ $currentStockEntry->quick_open_amount_qu_consume }}</span>
</a>
@endif
@endif