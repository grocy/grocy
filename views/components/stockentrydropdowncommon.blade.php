		<a class="dropdown-item show-as-dialog-link permission-SHOPPINGLIST_ITEMS_ADD"
			type="button"
			data-href="{{ $U('/shoppinglistitem/new?embedded&updateexistingproduct&product=PRODUCT_ID') }}">
			<span class="dropdown-item-icon"><i class="fas fa-shopping-cart"></i></span> <span class="dropdown-item-text">{{ $__t('Add to shopping list') }}</span>
		</a>
		<div class="dropdown-divider"></div>
		<a class="dropdown-item show-as-dialog-link permission-STOCK_PURCHASE"
			type="button"
			data-href="{{ $U('/purchase?embedded&product=PRODUCT_ID' ) }}">
			<span class="dropdown-item-icon"><i class="fas fa-cart-plus"></i></span> <span class="dropdown-item-text">{{ $__t('Purchase') }}</span>
		</a>
		<a class="dropdown-item show-as-dialog-link permission-STOCK_CONSUME"
			type="button"
			data-disable="consume"
			data-href="{{ $U('/consume?embedded&product=PRODUCT_ID&locationId=LOCATON_ID&stockId=STOCK_ID') }}">
			<span class="dropdown-item-icon"><i class="fas fa-utensils"></i></span> <span class="dropdown-item-text">{{ $__t('Consume') }}</span>
		</a>
		@if(GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING)
		<a class="dropdown-item show-as-dialog-link permission-STOCK_TRANSFER"
			data-disable="transfer"
			type="button"
			data-href="{{ $U('/transfer?embedded&product=PRODUCT_ID&locationId=LOCATON_ID&stockId=STOCK_ID') }}">
			<span class="dropdown-item-icon"><i class="fas fa-exchange-alt"></i></span> <span class="dropdown-item-text">{{ $__t('Transfer') }}</span>
		</a>
		@endif
		<a class="dropdown-item show-as-dialog-link permission-STOCK_INVENTORY"
			type="button"
			data-href="{{ $U('/inventory?embedded&product=PRODUCT_ID') }}">
			<span class="dropdown-item-icon"><i class="fas fa-list"></i></span> <span class="dropdown-item-text">{{ $__t('Inventory') }}</span>
		</a>
		<div class="dropdown-divider"></div>
		<a class="dropdown-item product-consume-button product-consume-button-spoiled permission-STOCK_CONSUME"
			type="button"
			href="#"
			data-product-id=""
			data-product-name=""
			data-product-qu-name=""
			data-consume-amount="1"
			data-stock-id=""
			data-stockrow-id=""
			data-location-id=""
			data-disable="consume">
			<span class="dropdown-item-text"
				data-compute='["Consume %1$s of %2$s as spoiled", "1 PRODUCT_QU_NAME" , "PRODUCT_NAME"]'></span>
		</a>
		@if(GROCY_FEATURE_FLAG_RECIPES)
		<a class="dropdown-item"
			type="button"
			data-href="{{ $U('/recipes?search=PRODUCT_NAME') }}">
			<span class="dropdown-item-text">{{ $__t('Search for recipes containing this product') }}</span>
		</a>
		@endif
		<div class="dropdown-divider"></div>
		<a class="dropdown-item product-name-cell"
			data-product-id="xxx"
			type="button"
			data-href="#">
			<span class="dropdown-item-text">{{ $__t('Product overview') }}</span>
		</a>
		@if(!isset($skipStockEntries))
		<a class="dropdown-item show-as-dialog-link"
			type="button"
			data-href="{{ $U('/stockentries?embedded&product=PRODUCT_ID') }}"
			data-product-id="xxx">
			<span class="dropdown-item-text">{{ $__t('Stock entries') }}</span>
		</a>
		@endif
		<a class="dropdown-item show-as-dialog-link"
			type="button"
			data-href="{{ $U('/stockjournal?embedded&product=PRODUCT_ID') }}">
			<span class="dropdown-item-text">{{ $__t('Stock journal') }}</span>
		</a>
		<a class="dropdown-item show-as-dialog-link"
			type="button"
			data-href="{{ $U('/stockjournal/summary?embedded&product_id=PRODUCT_ID') }}">
			<span class="dropdown-item-text">{{ $__t('Stock journal summary') }}</span>
		</a>
		<a class="dropdown-item permission-MASTER_DATA_EDIT"
			type="button"
			data-href="{{ $U('/product/PRODUCT_ID') }}RETURNTO">
			<span class="dropdown-item-text">{{ $__t('Edit product') }}</span>
		</a>