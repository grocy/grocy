@extends($rootLayout)

@if($mode == 'edit')
@section('title', $__t('Edit %s', $userentity->caption))
@else
@section('title', $__t('Create %s', $userentity->caption))
@endif

@section('viewJsName', 'userobjectform')

@section('grocyConfigProps')
EditMode: '{{ $mode }}',
EditObjectParentId: {{ $userentity->id }},
EditObjectParentName: "{{ $userentity->name }}",
@if($mode == 'edit')	
EditObjectId: {{ $userobject->id }},
@endif
@endsection

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

@php
$classes = $embedded ? '' : 'col-lg-6';
@endphp

<div class="row">
	<div class="{{ $classes }} col-12">
		<form id="userobject-form"
			novalidate>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'userentity-' . $userentity->name
			))

			<button id="save-userobject-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
