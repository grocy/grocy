@extends('layout.default')

@section('title', $__t('Last Used Report'))
@section('viewJsName', 'stockreportlastused')

@section('content')
<div class="row">
    <div class="col">
        <h2 class="title">@yield('title')</h2>
    </div>
</div>

<hr class="my-2">

<div class="row">
    <div class="col-xs-12 col-md-8 pb-3">
        <div class="table-responsive">
            <table id="stock-last-used-table" class="table table-striped dt-responsive">
                <thead>
                    <tr>
                        <th>{{ $__t('Product') }}</th>
                        <th>{{ $__t('Amount in stock') }}</th>
                        <th>{{ $__t('Last used') }}</th>
                    </tr>
                </thead>
                <tbody class="d-none">
                    @foreach($products as $product)
                    <tr>
                        <td>
                            {{ $product->name }}
                        </td>
                        <td>
                            {{ $product->amount }}
                        </td>
                        <td>
                            @if($product->last_used)
                                {{ $product->last_used }}
                                <time class="timeago timeago-contextual" datetime="{{ $product->last_used }}"></time>
                            @else
                                {{ $__t('Never') }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
