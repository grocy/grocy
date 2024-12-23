@extends('layout.default')

@if($mode == 'edit')
@section('title', $__t('Edit userentity'))
@else
@section('title', $__t('Create userentity'))
@endif

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<script>
			Grocy.EditMode = '{{ $mode }}';
		</script>

		@if($mode == 'edit')
		<script>
			Grocy.EditObjectId = {{ $userentity->id }};
		</script>
		@endif

		<form id="userentity-form"
			novalidate>

			<div class="form-group">
				<label for="name">{{ $__t('Name') }}</label>
				<input @if($mode=='edit'
					)
					disabled
					@endif
					type="text"
					class="form-control"
					required
					pattern="^[a-zA-Z0-9_]*$"
					id="name"
					name="name"
					value="@if($mode == 'edit'){{ $userentity->name }}@endif">
				<div class="invalid-feedback">{{ $__t('This is required and can only contain letters and numbers') }}</div>
			</div>

			<div class="form-group">
				<label for="name">{{ $__t('Caption') }}</label>
				<input type="text"
					class="form-control"
					required
					id="caption"
					name="caption"
					value="@if($mode == 'edit'){{ $userentity->caption }}@endif">
				<div class="invalid-feedback">{{ $__t('A caption is required') }}</div>
			</div>

			<div class="form-group">
				<label for="description">{{ $__t('Description') }}</label>
				<textarea class="form-control"
					rows="2"
					id="description"
					name="description">@if($mode == 'edit'){{ $userentity->description }}@endif</textarea>
			</div>

			<div class="form-group">
				<div class="custom-control custom-checkbox">
					<input @if($mode=='edit'
						&&
						$userentity->show_in_sidebar_menu == 1) checked @endif class="form-check-input custom-control-input" type="checkbox" id="show_in_sidebar_menu" name="show_in_sidebar_menu" value="1">
					<label class="form-check-label custom-control-label"
						for="show_in_sidebar_menu">{{ $__t('Show in sidebar menu') }}</label>
				</div>
			</div>

			<div class="form-group">
				<label for="name">{{ $__t('Icon CSS class') }}</label>
				<input type="text"
					class="form-control"
					id="icon_css_class"
					name="icon_css_class"
					value="@if($mode == 'edit'){{ $userentity->icon_css_class }}@endif"
					placeholder='{{ $__t('
					For
					example')
					}} "fa-solid fa-smile"'>
			</div>

			<button id="save-userentity-button"
				class="btn btn-success">{{ $__t('
					Save')
					}}</button>

		</form>
	</div>
</div>
@stop
