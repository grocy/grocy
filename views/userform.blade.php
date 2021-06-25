@extends($rootLayout)

@if($mode == 'edit')
@section('title', $__t('Edit user'))
@else
@section('title', $__t('Create user'))
@endif

@section('viewJsName', 'userform')

@section('grocyConfigProps')
EditMode: '{{ $mode }}',
@if($mode == 'edit')	
	EditObjectId: {{ $user->id }},
	@if(!empty($user->picture_file_name))
		UserPictureFileName: '{{ $user->picture_file_name }}',
	@endif
@endif
@endsection

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col-lg-6 col-12">
		<form id="user-form"
			novalidate>

			<div class="form-group">
				<label for="username">{{ $__t('Username') }}</label>
				<input type="text"
					class="form-control"
					required
					id="username"
					name="username"
					value="@if($mode == 'edit'){{ $user->username }}@endif">
				<div class="invalid-feedback">{{ $__t('A username is required') }}</div>
			</div>

			<div class="form-group">
				<label for="first_name">{{ $__t('First name') }}</label>
				<input type="text"
					class="form-control"
					id="first_name"
					name="first_name"
					value="@if($mode == 'edit'){{ $user->first_name }}@endif">
			</div>

			<div class="form-group">
				<label for="last_name">{{ $__t('Last name') }}</label>
				<input type="text"
					class="form-control"
					id="last_name"
					name="last_name"
					value="@if($mode == 'edit'){{ $user->last_name }}@endif">
			</div>

			<div class="form-group">
				<label for="password">{{ $__t('Password') }}</label>
				<input type="password"
					class="form-control"
					required
					id="password"
					name="password">
			</div>

			<div class="form-group">
				<label for="password_confirm">{{ $__t('Confirm password') }}</label>
				<input type="password"
					class="form-control"
					required
					id="password_confirm"
					name="password_confirm">
				<div class="invalid-feedback">{{ $__t('Passwords do not match') }}</div>
			</div>

			@include('components.userfieldsform', array(
			'userfields' => $userfields,
			'entity' => 'users'
			))

			<button id="save-user-button"
				class="btn btn-success">{{ $__t('Save') }}</button>

		</form>
	</div>

	<div class="col-lg-6 col-12">
		<div class="title-related-links">
			<h4>
				{{ $__t('Picture') }}
			</h4>
			<div class="form-group w-75 m-0">
				<div class="input-group">
					<div class="custom-file">
						<input type="file"
							class="custom-file-input"
							id="user-picture"
							accept="image/*">
						<label id="user-picture-label"
							class="custom-file-label @if(empty($user->picture_file_name)) d-none @endif"
							for="user-picture">
							{{ $user->picture_file_name }}
						</label>
						<label id="user-picture-label-none"
							class="custom-file-label @if(!empty($user->picture_file_name)) d-none @endif"
							for="user-picture">
							{{ $__t('No file selected') }}
						</label>
					</div>
					<div class="input-group-append">
						<span class="input-group-text"><i class="fas fa-trash"
								id="delete-current-user-picture-button"></i></span>
					</div>
				</div>
			</div>
		</div>
		@if(!empty($user->picture_file_name))
		<img id="current-user-picture"
			data-src="{{ $U('/api/files/userpictures/' . base64_encode($user->picture_file_name) . '?force_serve_as=picture&best_fit_width=400') }}"
			class="img-fluid img-thumbnail mt-2 lazy mb-5">
		<p id="delete-current-user-picture-on-save-hint"
			class="form-text text-muted font-italic d-none mb-5">{{ $__t('The current picture will be deleted on save') }}</p>
		@else
		<p id="no-current-user-picture-hint"
			class="form-text text-muted font-italic mb-5">{{ $__t('No picture available') }}</p>
		@endif
	</div>
</div>
@stop
