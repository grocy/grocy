@extends('errors.base')

@section('title', $__t('Server error'))

@section('content')
<div class="row">
	<div class="col">
		<h1 class="alert alert-danger">{{ $__t('A server error occured while processing your request') }}</h1>
		<div class="alert alert-info">
			{{ $__t('If you think this is a bug, please report it') }}<br>
			&rarr; <a target="_blank"
				href="https://github.com/grocy/grocy/issues">https://github.com/grocy/grocy/issues</a>
		</div>
	</div>
</div>
@parent
@stop
