@extends('layout.default')

@section('title', $__t('Shopping list settings'))

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-4 col-md-8 col-12">
		<h4>{{ $__t('Shopping list') }}</h4>

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="shopping_list_show_calendar"
					data-setting-key="shopping_list_show_calendar">
				<label class="form-check-label custom-control-label"
					for="shopping_list_show_calendar">
					{{ $__t('Show a month-view calendar') }}
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="shopping_list_round_up"
					data-setting-key="shopping_list_round_up">
				<label class="form-check-label custom-control-label"
					for="shopping_list_round_up">
					{{ $__t('Round up quantity amounts to the nearest whole number') }}
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="shopping_list_auto_add_below_min_stock_amount"
					data-setting-key="shopping_list_auto_add_below_min_stock_amount">
				<label class="form-check-label custom-control-label"
					for="shopping_list_auto_add_below_min_stock_amount">
					{{ $__t('Automatically add products that are below their defined min. stock amount to the shopping list') }}
					<select class="custom-control custom-select user-setting-control"
						id="shopping_list_auto_add_below_min_stock_amount_list_id"
						data-setting-key="shopping_list_auto_add_below_min_stock_amount_list_id"
						@if(!boolval($userSettings['shopping_list_auto_add_below_min_stock_amount']))
						disabled
						@endif>
						@foreach($shoppingLists as $shoppingList)
						<option value="{{ $shoppingList->id }}">{{ $shoppingList->name }}</option>
						@endforeach
					</select>
				</label>
			</div>
		</div>


		<h4 class="mt-5">{{ $__t('Shopping list to stock workflow') }}</h4>

		<div class="form-group">
			<div class="custom-control custom-checkbox">
				<input type="checkbox"
					class="form-check-input custom-control-input user-setting-control"
					id="shopping_list_to_stock_workflow_auto_submit_when_prefilled"
					data-setting-key="shopping_list_to_stock_workflow_auto_submit_when_prefilled">
				<label class="form-check-label custom-control-label"
					for="shopping_list_to_stock_workflow_auto_submit_when_prefilled">
					{{ $__t('Automatically do the booking using the last price and the amount of the shopping list item, if the product has "Default due days" set') }}
				</label>
			</div>
		</div>

		<a href="{{ $U('/shoppinglist') }}"
			class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
