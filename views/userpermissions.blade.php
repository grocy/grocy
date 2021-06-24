@extends($rootLayout)

@section('title', $__t('Permissions for user %s', GetUserDisplayName($user)))
@section('activeNav', '')
@section('viewJsName', 'userpermissions')

@section('grocyConfigProps')
EditMode: '{{ $mode }}',
EditObjectId: {{ $user->id }},
@endsection

@push('pageStyles')
<style>
	ul {
		list-style-type: none;
	}

</style>
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>

<hr class="my-2">

<div class="row">
	<div class="col">
		<ul class="pl-0">
			@foreach($permissions as $perm)
			<li>
				@include('components.userpermission_select', array(
				'permission' => $perm
				))
			</li>
			@endforeach
		</ul>
		<button id="permission-save"
			class="btn btn-success"
			type="submit">{{ $__t('Save') }}</button>
	</div>
</div>
@endsection
