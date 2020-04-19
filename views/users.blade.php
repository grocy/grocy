@extends('layout.default')

@section('title', $__t('Users'))
@section('activeNav', '')
@section('viewJsName', 'users')

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>
<hr>
<div class="row mt-3">
	<div class="col-xs-12 col-md-2 col-xl-1">
		<a class="btn btn-primary btn-sm responsive-button w-100 mb-3" href="{{ $U('/user/new') }}">
			{{ $__t('Add') }}
		</a>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<div class="input-group mb-3">
			<div class="input-group-prepend">
					<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"  id="search" class="form-control" placeholder="{{ $__t('Search') }}">
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="users-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
					<th>{{ $__t('Username') }}</th>
					<th>{{ $__t('First name') }}</th>
					<th>{{ $__t('Last name') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($users as $user)
				<tr>
					<td class="fit-content border-right">
						<a class="btn btn-info btn-sm" href="{{ $U('/user/') }}{{ $user->id }}">
							<i class="fas fa-edit"></i>
						</a>
						<a class="btn btn-danger btn-sm user-delete-button @if($user->id == GROCY_USER_ID) disabled @endif" href="#" data-user-id="{{ $user->id }}" data-user-username="{{ $user->username }}">
							<i class="fas fa-trash"></i>
						</a>
					</td>
					<td>
						{{ $user->username }}
					</td>
					<td>
						{{ $user->first_name }}
					</td>
					<td>
						{{ $user->last_name }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
