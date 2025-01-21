@php require_frontend_packages(['datatables', 'bwipjs']); @endphp

@extends('layout.default')

@section('title', $__t('API keys'))

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
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
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a id="add-api-key-button"
					class="btn btn-primary responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="#">
					{{ $__t('Add') }}
				</a>
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
		<table id="apikeys-table"
			class="table table-sm table-striped nowrap w-100">
			<thead>
				<tr>
					<th class="border-right"><a class="text-muted change-table-columns-visibility-button"
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#apikeys-table"
							href="#"><i class="fa-solid fa-eye"></i></a>
					</th>
					<th>{{ $__t('Description') }}</th>
					<th>{{ $__t('API key') }}</th>
					<th class="allow-grouping">{{ $__t('User') }}</th>
					<th>{{ $__t('Expires') }}</th>
					<th>{{ $__t('Last used') }}</th>
					<th>{{ $__t('Created') }}</th>
					<th class="allow-grouping">{{ $__t('Key type') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($apiKeys as $apiKey)
				<tr class="@if($apiKey->id == $selectedKeyId) table-info @endif">
					<td class="fit-content border-right">
						<a class="btn btn-danger btn-sm apikey-delete-button"
							href="#"
							data-apikey-id="{{ $apiKey->id }}"
							data-apikey-key="{{ $apiKey->api_key }}"
							data-apikey-description="{{ $apiKey->description }}"
							data-toggle="tooltip"
							title="{{ $__t('Delete this item') }}">
							<i class="fa-solid fa-trash"></i>
						</a>
						<a class="btn btn-info btn-sm apikey-show-qr-button"
							href="#"
							data-apikey-key="{{ $apiKey->api_key }}"
							data-apikey-type="{{ $apiKey->key_type }}"
							data-apikey-description="{{ $apiKey->description }}"
							data-toggle="tooltip"
							title="{{ $__t('Show a QR-Code for this API key') }}">
							<i class="fa-solid fa-qrcode"></i>
						</a>
					</td>
					<td>
						{{ $apiKey->description }}
					</td>
					<td>
						{{ $apiKey->api_key }}
					</td>
					<td>
						{{ GetUserDisplayName(FindObjectInArrayByPropertyValue($users, 'id', $apiKey->user_id)) }}
					</td>
					<td>
						{{ $apiKey->expires }}
						<time class="timeago timeago-contextual"
							datetime="{{ $apiKey->expires }}"></time>
					</td>
					<td>
						@if(empty($apiKey->last_used)){{ $__t('never') }}@else{{ $apiKey->last_used }}@endif
						<time class="timeago timeago-contextual"
							datetime="{{ $apiKey->last_used }}"></time>
					</td>
					<td>
						{{ $apiKey->row_created_timestamp }}
						<time class="timeago timeago-contextual"
							datetime="{{ $apiKey->row_created_timestamp }}"></time>
					</td>
					<td>
						{{ $apiKey->key_type }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade"
	id="add-api-key-modal"
	tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title w-100">{{ $__t('Create new API key') }}</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="name">{{ $__t('Description') }}</label>
					<input type="text"
						class="form-control"
						id="description"
						name="description">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-dismiss="modal">{{ $__t('Cancel') }}</button>
				<button id="new-api-key-button"
					class="btn btn-primary">{{ $__t('OK') }}</button>
			</div>
		</div>
	</div>
</div>
@stop
