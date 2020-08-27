<label>
    <input type="checkbox" name="{{ $perm->permission_name }}" class="permission-cb" data-perm-id="{{ $perm->permission_id }}" @if($perm->has_permission) checked @endif autocomplete="off">
    {{ $__t($perm->permission_name) }}
</label>
<div id="permission-sub-{{ $perm->permission_name }}">
    <ul>
        @foreach($perm->uihelper_permissionList(array('user_id' => $user->id))->via('parent') as $p)
            <li>
                @include('components.permission_select', array(
                        'perm' =>  $p
                       ))
            </li>
        @endforeach
    </ul>
</div>