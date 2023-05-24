@extends('layout.default')

@section('content')
<div class="row">
	<div class="col">
		<div class="alert alert-dark py-1">
			<h4>{{ $__t('Error source') }}</h4>
			<pre class="my-0"><code>{!! $exception->getFile() !!}:{!! $exception->getLine() !!}</code></pre>
		</div>
		<div class="alert alert-dark py-1">
			<h4>{{ $__t('Error message') }}</h4>
			<pre class="my-0"><code>{!! $exception->getMessage() !!}</code></pre>
		</div>
		<div class="alert alert-dark py-1">
			<h4>{{ $__t('Stack trace') }}</h4>
			<pre class="my-0"><code>{!! $exception->getTraceAsString() !!}</code></pre>
		</div>
		<div class="alert alert-dark py-1">
			<h4>{{ $__t('Easy error info copy & paste (for reporting)') }}</h4>
			<textarea class="form-control easy-link-copy-textbox text-monospace mt-1"
				rows="20">
Error source:
```
{!! $exception->getFile() !!}:{!! $exception->getLine() !!}
```

Error message:
```
{!! $exception->getMessage() !!}
```

Stack trace:
```
{!! $exception->getTraceAsString() !!}
```

System info:
```
{!! json_encode($systemInfo, JSON_PRETTY_PRINT) !!}
```
			</textarea>
		</div>
	</div>
</div>
@stop
