@extends('errors.base')

@section('title', $__t('Unauthorized'))

@section('content')
<div class="row">
	<div class="col text-center">
		<h1 class="alert alert-danger">{{ $__t('You are not allowed to view this page') }}</h1>
	</div>
</div>
@stop
