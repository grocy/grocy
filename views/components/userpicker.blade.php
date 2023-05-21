@php require_frontend_packages(['bootstrap-combobox']); @endphp

@once
@push('componentScripts')
<script src="{{ $U('/viewjs/components/userpicker.js', true) }}?v={{ $version }}"></script>
@endpush
@endonce

@php if(empty($prefillByUsername)) { $prefillByUsername = ''; } @endphp
@php if(empty($prefillByUserId)) { $prefillByUserId = ''; } @endphp
@php if(!isset($nextInputSelector)) { $nextInputSelector = ''; } @endphp

<div class="form-group"
	data-next-input-selector="{{ $nextInputSelector }}"
	data-prefill-by-username="{{ $prefillByUsername }}"
	data-prefill-by-user-id="{{ $prefillByUserId }}">
	<label for="user_id">{{ $__t($label) }}</label>
	<select class="form-control user-combobox"
		id="user_id"
		name="user_id">
		<option value=""></option>
		@foreach($users as $user)
		<option data-additional-searchdata="{{ $user->username }}"
			value="{{ $user->id }}">{{ GetUserDisplayName($user) }}</option>
		@endforeach
	</select>
</div>
