@extends('layout.default')

@section('title', $__t('API keys'))
@section('activeNav', '')
@section('viewJsName', 'manageapikeys')

@push('pageStyles')
	<link href="{{ $U('/node_modules/animate.css/animate.min.css?v=', true) }}{{ $version }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
	<div class="col">
		<h2 class="title">@yield('title')</h2>
	</div>
</div>
<hr>
<div class="row mt-3">
	<div class="col-xs-12 col-md-2 col-xl-1">
		<a class="btn btn-primary btn-sm responsive-button w-100 mb-3" href="{{ $U('/manageapikeys/new') }}">
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
		<table id="apikeys-table" class="table table-sm table-striped dt-responsive">
			<thead>
				<tr>
					<th class="border-right"></th>
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
						<a class="btn btn-danger btn-sm apikey-delete-button" href="#" data-apikey-id="{{ $apiKey->id }}" data-apikey-apikey="{{ $apiKey->api_key }}">
							<i class="fas fa-trash"></i>
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
						<time class="timeago timeago-contextual" datetime="{{ $apiKey->expires }}"></time>
					</td>
					<td>
						@if(empty($apiKey->last_used)){{ $__t('never') }}@else{{ $apiKey->last_used }}@endif
						<time class="timeago timeago-contextual" datetime="{{ $apiKey->last_used }}"></time>
					</td>
					<td>
						{{ $apiKey->row_created_timestamp }}
						<time class="timeago timeago-contextual" datetime="{{ $apiKey->row_created_timestamp }}"></time>
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
