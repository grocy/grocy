@extends('layout.default')

@section('title', $__t('Users'))
@section('activeNav', '')
@section('viewJsName', 'users')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/user/new') }}">
				<i class="fas fa-plus"></i>&nbsp;{{ $__t('Add') }}
			</a>
		</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $__t('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
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
