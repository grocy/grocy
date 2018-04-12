@extends('layout.default')

@section('title', 'Habits')
@section('activeNav', 'habits')
@section('viewJsName', 'habits')

@section('content')
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

	<h1 class="page-header">
		Habits
		<a class="btn btn-default" href="/habit/new" role="button">
			<i class="fa fa-plus"></i>&nbsp;Add
		</a>
	</h1>

	<div class="table-responsive">
		<table id="habits-table" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Period type</th>
					<th>Period days</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				@foreach($habits as $habit)
				<tr>
					<td class="fit-content">
						<a class="btn btn-info" href="/habit/{{ $habit->id }}" role="button">
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
						{{ $habit->period_type }}
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
