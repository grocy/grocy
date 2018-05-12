@extends('layout.default')

@section('title', $L('API keys'))
@section('activeNav', '')
@section('viewJsName', 'manageapikeys')

@push('pageScripts')
	<script src="{{ $U('/bower_components/jquery-ui/jquery-ui.min.js?v=') }}{{ $version }}"></script>
@endpush

@section('content')
<h1 class="page-header">
	@yield('title')
	<a class="btn btn-default" href="{{ $U('/manageapikeys/new') }}" role="button">
		<i class="fa fa-plus"></i>&nbsp;{{ $L('Create new API key') }}
	</a>
</h1>

<p class="lead"><a href="{{ $U('/api') }}" target="_blank">{{ $L('REST API & data model documentation') }}</a></p>

<div class="table-responsive">
	<table id="apikeys-table" class="table table-striped">
		<thead>
			<tr>
				<th>#</th>
				<th>{{ $L('API key') }}</th>
				<th>{{ $L('Expires') }}</th>
				<th>{{ $L('Last used') }}</th>
				<th>{{ $L('Created') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($apiKeys as $apiKey)
			<tr id="apiKeyRow_{{ $apiKey->id }}">
				<td class="fit-content">
					<a class="btn btn-danger apikey-delete-button" href="#" role="button" data-apikey-id="{{ $apiKey->id }}" data-apikey-apikey="{{ $apiKey->api_key }}">
						<i class="fa fa-trash"></i>
					</a>
				</td>
				<td>
					{{ $apiKey->api_key }}
				</td>
				<td>
					{{ $apiKey->expires }}
					<time class="timeago timeago-contextual" datetime="{{ $apiKey->expires }}"></time>
				</td>
				<td>
					@if(empty($apiKey->last_used)){{ $L('never') }}@else{{ $apiKey->last_used }}@endif
					<time class="timeago timeago-contextual" datetime="{{ $apiKey->last_used }}"></time>
				</td>
				<td>
					{{ $apiKey->row_created_timestamp }}
					<time class="timeago timeago-contextual" datetime="{{ $apiKey->row_created_timestamp }}"></time>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@stop
