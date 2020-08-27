@extends('errors.base')

@section('title', $__t('Page not found'))
@section('content')
    <meta http-equiv="refresh" content="5;url=/">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h2 class="title">@yield('title')</h2>
            <div>
                {!! nl2br(e($exception->getTraceAsString())) !!}
            </div>
        </div>
    </div>
@stop
