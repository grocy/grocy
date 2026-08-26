@php require_frontend_packages(['datatables', 'animatecss']); @endphp

@extends('layout.default')

@section('title', $__t('Sessions'))

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">
				@yield('title')
				<span class="text-muted small">{{ $__t('User') }} <strong>{{ GetUserDisplayName($user) }}</strong></span>
			</h2>
			<div class="float-right @if($embedded) pr-5 @endif">
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#table-filter-row">
					<i class="fa-solid fa-filter"></i>
				</button>
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#related-links">
					<i class="fa-solid fa-ellipsis-v"></i>
				</button>
			</div>
		</div>
	</div>
</div>

<hr class="my-2">

<div class="row collapse d-md-flex"
	id="table-filter-row">
	<div class="col-12 col-md-6 col-xl-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa-solid fa-search"></i></span>
			</div>
			<input type="text"
				id="search"
				class="form-control"
				placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col">
		<div class="float-right">
			<button id="clear-filter-button"
				class="btn btn-sm btn-outline-info"
				data-toggle="tooltip"
				title="{{ $__t('Clear filter') }}">
				<i class="fa-solid fa-filter-circle-xmark"></i>
			</button>
		</div>
	</div>
</div>

<div class="row">
	<div class="col">
		<table id="sessions-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#sessions-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th class="allow-grouping">{{ $__t('Token type') }}</th>
					<th>{{ $__t('Token hash') }}</th>
					<th>{{ $__t('Created') }}</th>
					<th>{{ $__t('Expires') }}</th>
					<th>{{ $__t('Last access') }}</th>
					<th>{{ $__t('Client') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($sessionRows as $sessionRow)
				<tr id="session-{{ $sessionRow->id }}-row">
					<td class="fit-content border-right">
						<a class="btn btn-danger btn-sm session-delete-button"
							href="#"
							data-session-id="{{ $sessionRow->id }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fa-solid fa-trash"></i>
						</a>
					</td>
					<td>
						@if($sessionRow->token_type == 1) access @elseif($sessionRow->token_type == 2) remember_me @endif
					</td>
					<td>
						{{ substr($sessionRow->token_hash, 0, 8) }} @if(in_array($sessionRow->token_hash, $thisSessionTokenHashes)) <span class="badge badge-info">{{ $__t('This session') }}</span> @endif
					</td>
					<td>
						{{ $sessionRow->row_created_timestamp }}
						<time class="timeago timeago-contextual"
							datetime="{{ $sessionRow->row_created_timestamp }}"></time>
					</td>
					<td>
						{{ $sessionRow->expires }}
						<time class="timeago timeago-contextual"
							datetime="{{ $sessionRow->expires }}"></time>
					</td>
					<td>
						@if(empty($sessionRow->last_used)){{ $__t('never') }}@else{{ $sessionRow->last_used }}@endif
						<time class="timeago timeago-contextual"
							datetime="{{ $sessionRow->last_used }}"></time>
					</td>
					<td>
						{{ $sessionRow->client_info }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@stop
