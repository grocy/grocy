@push('componentScripts')
	<script src="{{ $U('/viewjs/components/userfieldsform.js', true) }}?v={{ $version }}"></script>
@endpush

@if(count($userfields) > 0)

@foreach($userfields as $userfield)

@if($userfield->show_as_column_in_tables == 1)
	<th>{{ $userfield->name }}</th>
@endif

@endforeach

@endif
