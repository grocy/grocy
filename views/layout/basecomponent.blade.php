@section('componentContent')
@show

@push('componentScripts')
	<script src="/viewjs/components/@yield('componentJsName').js"></script>
@endpush
