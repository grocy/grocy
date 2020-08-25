@extends('layout.default')

@section('title', $__t('Permissions of %s', GetUserDisplayName($user)))
@section('activeNav', '')
@section('viewJsName', 'permissions')
@push('pageScripts')
    <script>
        var edited_user_id = {{ $user->id }};
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col">
            <h2 class="title">@yield('title')</h2>
        </div>
    </div>
    <hr>
    <div class="row">
        <div>
            <ul>
                @foreach($permissions as $perm)
                    <li>
                        @include('components.permission_select', array(
                            'permission' =>  $perm
                           ))
                    </li>
                @endforeach
            </ul>
            <button id="permission-save" class="btn btn-success" type="submit">{{ $__t('Save') }}</button>
        </div>
    </div>
@endsection