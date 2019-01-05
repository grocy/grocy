@extends('layout.default')

@section('title', $L('Chores'))
@section('activeNav', 'chores')
@section('viewJsName', 'chores')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/chore/new') }}">
				<i class="fas fa-plus"></i>&nbsp;{{ $L('Add') }}
			</a>
		</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-xs-12 col-md-6 col-xl-3">
		<label for="search">{{ $L('Search') }}</label> <i class="fas fa-search"></i>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="chores-table" class="table table-sm table-striped dt-responsive">
		<thead>
			<tr>
				<th>#</th>
				<th>{{ $L('Name') }}</th>
				<th>{{ $L('Period type') }}</th>
				<th>{{ $L('Period days') }}</th>
				<th>{{ $L('Description') }}</th>
			</tr>
		</thead>
		<tbody class="d-none">
			@foreach($chores as $chore)
			<tr>
				<td class="fit-content">
					<a class="btn btn-info btn-sm" href="{{ $U('/chore/') }}{{ $chore->id }}">
						<i class="fas fa-edit"></i>
					</a>
					<a class="btn btn-danger btn-sm chore-delete-button" href="#" data-chore-id="{{ $chore->id }}" data-chore-name="{{ $chore->name }}">
						<i class="fas fa-trash"></i>
					</a>
				</td>
				<td>
					{{ $chore->name }}
				</td>
				<td>
					{{ $L($chore->period_type) }}
				</td>
				<td>
					{{ $chore->period_days }}
				</td>
				<td>
					{{ $chore->description }}
				</td>
			</tr>
			@endforeach
		</tbody>
		</table>
	</div>
</div>
@stop
