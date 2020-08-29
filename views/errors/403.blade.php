@extends('errors.base')

@section('title', $__t('Unauthorized'))

@section('content')
    <div class="row">
        <div class="col">
            <div class="alert alert-danger">{{ $__t('You are not allowed to view this page') }}</div>
        </div>
    </div>
@stop
