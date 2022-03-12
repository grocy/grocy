@extends('layout.default')

@section('title', $__t('Products'))
@section('activeNav', 'products')
@section('viewJsName', 'products')

@section('content')
    <div class="row">
        <div class="col">
            <div class="title-related-links">
                <h2 class="title">@yield('title')</h2>
                <div class="float-right">
                    <button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3" type="button"
                        data-toggle="collapse" data-target="#table-filter-row">
                        <i class="fas fa-filter"></i>
                    </button>
                    <button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3" type="button"
                        data-toggle="collapse" data-target="#related-links">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
                <div class="related-links collapse d-md-flex order-2 width-xs-sm-100" id="related-links">
                    <a class="btn btn-primary responsive-button m-1 mt-md-0 mb-md-0 float-right"
                        href="{{ $U('/product/new') }}">
                        {{ $__t('Add') }}
                    </a>
                    <a class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
                        href="{{ $U('/userfields?entity=products') }}">
                        {{ $__t('Configure userfields') }}
                    </a>
                    <a class="btn btn-outline-secondary m-1 mt-md-0 mb-md-0 float-right"
                        href="{{ $U('/stocksettings#productpresets') }}">
                        {{ $__t('Presets for new products') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-2">

    <div class="row collapse d-md-flex mb-3" id="table-filter-row">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" id="search" class="form-control" placeholder="{{ $__t('Search') }}">
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-filter"></i>&nbsp;{{ $__t('Product group') }}</span>
                </div>
                {{-- TODO: Select2: dynamic data: product_groups --}}
                <select class="custom-control custom-select" id="product-group-filter">
                    <option value="all">{{ $__t('All') }}</option>
                    @foreach ($productGroups as $productGroup)
                        <option value="{{ $productGroup->id }}">{{ $productGroup->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-2">
            <div class="form-check custom-control custom-checkbox">
                <input class="form-check-input custom-control-input" type="checkbox" id="show-disabled">
                <label class="form-check-label custom-control-label" for="show-disabled">
                    {{ $__t('Show disabled') }}
                </label>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-2">
            <div class="form-check custom-control custom-checkbox">
                <input class="form-check-input custom-control-input" type="checkbox" id="show-only-in-stock">
                <label class="form-check-label custom-control-label" for="show-only-in-stock">
                    {{ $__t('Show only in-stock products') }}
                </label>
            </div>
        </div>
        <div class="col">
            <div class="float-right">
                <a id="clear-filter-button" class="btn btn-sm btn-outline-info" href="#">
                    {{ $__t('Clear filter') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table id="products-table" class="table table-sm table-striped nowrap w-100">
                <thead>
                    <tr>
                        <th class="border-right"><a class="text-muted change-table-columns-visibility-button"
                                data-toggle="tooltip" data-toggle="tooltip" title="{{ $__t('Table options') }}"
                                data-table-selector="#products-table" href="#"><i class="fas fa-eye"></i></a>
                        </th>
                        <th>{{ $__t('Name') }}</th>
                        <th class="@if (!GROCY_FEATURE_FLAG_STOCK_LOCATION_TRACKING) d-none @endif allow-grouping">{{ $__t('Location') }}
                        </th>
                        <th class="allow-grouping">{{ $__t('Min. stock amount') }}</th>
                        <th class="">{{ $__t('Default quantity unit purchase') }}</th>
                        <th class="allow-grouping">{{ $__t('Quantity unit stock') }}</th>
                        <th class="">{{ $__t('Product group') }}</th>
                        <th class="@if (!GROCY_FEATURE_FLAG_STOCK_PRICE_TRACKING) d-none @endif allow-grouping">
                            {{ $__t('Default store') }}</th>
                        @include('components.userfields_thead', [
                            'userfields' => $userfields,
                        ])
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="merge-products-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h4 class="modal-title w-100">{{ $__t('Merge products') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="merge-products-keep">{{ $__t('Product to keep') }}&nbsp;<i
                                class="fas fa-question-circle text-muted" data-toggle="tooltip" data-trigger="hover click"
                                title="{{ $__t('After merging, this product will be kept') }}"></i>
                        </label>
                        <select class="select2 custom-control custom-select" id="merge-products-keep"></select>
                    </div>
                    <div class="form-group">
                        <label for="merge-products-remove">{{ $__t('Product to remove') }}&nbsp;<i
                                class="fas fa-question-circle text-muted" data-toggle="tooltip" data-trigger="hover click"
                                title="{{ $__t('After merging, all occurences of this product will be replaced by "Product to keep" (means this product will not exist anymore)') }}"></i>
                        </label>
                        <select class="select2 custom-control custom-select" id="merge-products-remove"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $__t('Cancel') }}</button>
                    <button id="merge-products-save-button" type="button" class="btn btn-primary"
                        data-dismiss="modal">{{ $__t('OK') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var userfields = {!! json_encode($userfields) !!};
    </script>
@stop
