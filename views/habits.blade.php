@extends('layout.default')

@section('title', $L('Habits'))
@section('activeNav', 'habits')
@section('viewJsName', 'habits')

@section('content')
<h1 class="page-header">
	@yield('title')
	<a class="btn btn-default" href="{{ $U('/habit/new') }}" role="button">
		<i class="fa fa-plus"></i>&nbsp;{{ $L('Add') }}
	</a>
</h1>

<div class="table-responsive">
	<table id="habits-table" class="table table-striped">
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
					<a class="btn btn-info" href="{{ $U('/habit/') }}{{ $habit->id }}" role="button">
						<i class="fa fa-pencil"></i>
					</a>
					<a class="btn btn-danger habit-delete-button" href="#" role="button" data-habit-id="{{ $habit->id }}" data-habit-name="{{ $habit->name }}">
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
@stop
