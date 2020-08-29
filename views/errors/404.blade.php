@extends('errors.base')

@section('title', $__t('Page not found'))

@section('content')
    <meta http-equiv="refresh" content="5;url={{$U('/')}}">
    <div class="row">
        <div class="col">
            <div class="alert alert-danger">{{ $__t('This page does not exists') }}</div>
            <div>{{ $__t('You will be redirected to the default page in %s seconds', '5') }}</div>
        </div>
    </div>
@stop
