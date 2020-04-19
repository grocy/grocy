@extends('layout.default')

@section('title', $__t('Shopping list settings'))

@section('viewJsName', 'shoppinglistsettings')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
		<hr>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-xs-12">
		<h4 class="mt-2">{{ $__t('Shopping list') }}</h4>
		<div class="form-group">
			<div class="checkbox">
				<label for="shopping-list-show-calendar">
					<input type="checkbox" class="user-setting-control" id="shopping-list-show-calendar" name="shopping-list-show-calendar" data-setting-key="shopping_list_show_calendar"> {{ $__t('Show a month-view calendar') }}
				</label>
			</div>
		</div>
		<div class="form-group">
			<div class="checkbox">
				<label for="shopping-list-disable-auto-compact-view-on-mobile">
					<input type="checkbox" class="user-setting-control" id="shopping-list-disable-auto-compact-view-on-mobile" name="shopping-list-disable-auto-compact-view-on-mobile" data-setting-key="shopping_list_disable_auto_compact_view_on_mobile"> {{ $__t('Don\'t automatically switch to the compact view on mobile devices') }}
				</label>
			</div>
		</div>

		<h4 class="mt-2">{{ $__t('Shopping list to stock workflow') }}</h4>
		<div class="form-group">
			<div class="checkbox">
				<label for="shopping-list-to-stock-workflow-auto-submit-when-prefilled">
					<input type="checkbox" class="user-setting-control" id="shopping-list-to-stock-workflow-auto-submit-when-prefilled" name="shopping-list-to-stock-workflow-auto-submit-when-prefilled" data-setting-key="shopping_list_to_stock_workflow_auto_submit_when_prefilled"> {{ $__t('Automatically do the booking using the last price and the amount of the shopping list item, if the product has "Default best before days" set') }}
				</label>
			</div>
		</div>

		<a href="{{ $U('/shoppinglist') }}" class="btn btn-success">{{ $__t('OK') }}</a>
	</div>
</div>
@stop
