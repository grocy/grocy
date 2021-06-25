@extends($rootLayout)

@section('title', $__t('API keys'))
@section('activeNav', '')
@section('viewJsName', 'manageapikeys')

@section('content')
<div class="row">
	<div class="col">
		<div class="title-related-links">
			<h2 class="title">@yield('title')</h2>
			<div class="float-right">
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#table-filter-row">
					<i class="fas fa-filter"></i>
				</button>
				<button class="btn btn-outline-dark d-md-none mt-2 order-1 order-md-3"
					type="button"
					data-toggle="collapse"
					data-target="#related-links">
					<i class="fas fa-ellipsis-v"></i>
				</button>
			</div>
			<div class="related-links collapse d-md-flex order-2 width-xs-sm-100"
				id="related-links">
				<a class="btn btn-primary responsive-button m-1 mt-md-0 mb-md-0 float-right"
					href="{{ $U('/manageapikeys/new') }}">
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
				<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text"
				id="search"
				class="form-control"
				placeholder="{{ $__t('Search') }}">
		</div>
	</div>
	<div class="col">
		<div class="float-right">
			<a id="clear-filter-button"
				class="btn btn-sm btn-outline-info"
				href="#">
				{{ $__t('Clear filter') }}
			</a>
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
							data-toggle="tooltip"
							title="{{ $__t('Table options') }}"
							data-table-selector="#apikeys-table"
							href="#"><i class="fas fa-eye"></i></a>
					</th>
					<th>{{ $__t('API key') }}</th>
					<th>{{ $__t('User') }}</th>
					<th>{{ $__t('Expires') }}</th>
					<th>{{ $__t('Last used') }}</th>
					<th>{{ $__t('Created') }}</th>
					<th>{{ $__t('Key type') }}</th>
				</tr>
			</thead>
			<tbody class="d-none">
				@foreach($apiKeys as $apiKey)
				<tr id="apiKeyRow_{{ $apiKey->id }}">
					<td class="fit-content border-right">
						<a class="btn btn-danger btn-sm apikey-delete-button"
							href="#"
							data-apikey-id="{{ $apiKey->id }}"
							data-apikey-apikey="{{ $apiKey->api_key }}"
							data-toggle="tooltip"
							data-boundary="viewport"
							title="{{ $__t('Delete this item') }}">
							<i class="fas fa-trash"></i>
						</a>
						<a class="btn btn-info btn-sm apikey-show-qr-button"
							href="#"
							data-apikey-key="{{ $apiKey->api_key }}"
							data-apikey-type="{{ $apiKey->key_type }}"
							data-toggle="tooltip"
							data-boundary="viewport"
							title="{{ $__t('Show a QR-Code for this API key') }}">
							<i class="fas fa-qrcode"></i>
						</a>
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
@stop
