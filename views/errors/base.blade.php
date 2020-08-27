@extends('layout.default')

@section('content')
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h2 class="title">@yield('title')</h2>
            <div>
                {!! nl2br(e($exception->getTraceAsString())) !!}
            </div>
        </div>
    </div>
@stop
