@extends('layout.default')

@section('title', $L('REST API documentation'))
@section('viewJsName', 'apidoc')

@section('content')
	<div id="swagger-ui"></div>
@stop

@push('pageStyles')
	<link href="{{ $U('/bower_components/swagger-ui/dist/swagger-ui.css?v=') }}{{ $version }}" rel="stylesheet">
@endpush

@push('pageScripts')
	<script src="{{ $U('/bower_components/swagger-ui/dist/swagger-ui-bundle.js?v=') }}{{ $version }}"></script>
	<script src="{{ $U('/bower_components/swagger-ui/dist/swagger-ui-standalone-preset.js?v=') }}{{ $version }}"></script>
@endpush
