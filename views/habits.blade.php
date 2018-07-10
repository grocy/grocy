@extends('layout.default')

@section('title', $L('Habits'))
@section('activeNav', 'habits')
@section('viewJsName', 'habits')

@section('content')
<div class="row">
	<div class="col">
		<h1>
			@yield('title')
			<a class="btn btn-outline-dark" href="{{ $U('/habit/new') }}">
				<i class="fa fa-plus"></i>&nbsp;{{ $L('Add') }}
			</a>
		</h1>
	</div>
</div>

<div class="row mt-3">
	<div class="col-3">
		<label for="search">{{ $L('Search') }}</label>
		<input type="text" class="form-control" id="search">
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="habits-table" class="table table-sm table-striped dt-responsive">
		<thead>
			<tr>
				<th>#</th>
				<th>{{ $L('Name') }}</th>
				<th>{{ $L('Period type') }}</th>
				<th>{{ $L('Period days') }}</th>
				<th>{{ $L('Description') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($habits as $habit)
			<tr>
				<td class="fit-content">
					<a class="btn btn-info btn-sm" href="{{ $U('/habit/') }}{{ $habit->id }}">
						<i class="fa fa-pencil"></i>
					</a>
					<a class="btn btn-danger btn-sm habit-delete-button" href="#" data-habit-id="{{ $habit->id }}" data-habit-name="{{ $habit->name }}">
						<i class="fa fa-trash"></i>
					</a>
				</td>
				<td>
					{{ $habit->name }}
				</td>
				<td>
					{{ $L($habit->period_type) }}
				</td>
				<td>
					{{ $habit->period_days }}
				</td>
				<td>
					{{ $habit->description }}
				</td>
			</tr>
			@endforeach
		</tbody>
		</table>
	</div>
</div>
@stop
