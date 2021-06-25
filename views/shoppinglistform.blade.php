@extends($rootLayout)

@if($mode == 'edit')
@section('title', $__t('Edit shopping list'))
@else
@section('title', $__t('Create shopping list'))
@endif

@section('viewJsName', 'shoppinglistform')

@section('grocyConfigProps')
EditMode: '{{ $mode }}',
@if($mode == 'edit')	
EditObjectId: {{ $shoppingList->id }},
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
		<form id="shopping-list-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input type="text"
					class="form-control"
					required
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $shoppingList->name }}@endif">
				<div class="invalid-feedback">{{ $__t('A name is required') }}</div>
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'shopping_lists'
			))

			<button id="save-shopping-list-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>
</div>
@stop
