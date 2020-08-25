@extends('errors.base')

@section('title', $__t('You are not allowed to view this page!'))

@section('content')
    <meta http-equiv="refresh" content="5;url=/">
    <div class="row">
        <div class="col-xs-12 col-md-6 text-center">
            <h2 class="title">@yield('title')</h2>
        </div>
    </div>
@stop
