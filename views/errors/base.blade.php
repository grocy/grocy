@extends('layout.default')

@section('content')
    <div class="row">
        <div class="col">
            <div>
                <h6>{{ $__t('Error source') }}</h6>
                <pre><code>{!! $exception->getFile() !!}:{!! $exception->getLine() !!}</code></pre>
            </div>
            <div>
                <h6>{{ $__t('Error message') }}</h6>
                <pre><code>{!! $exception->getMessage() !!}</code></pre>
            </div>
            <div>
                <h6>{{ $__t('Stack trace') }}</h6>
                <pre><code>{!! $exception->getTraceAsString() !!}</code></pre>
            </div>
        </div>
    </div>
@stop
