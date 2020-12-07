<label>
	<input type="checkbox"
		name="{{ $perm->permission_name }}"
		class="permission-cb"
		data-perm-id="{{ $perm->permission_id }}"
		@if($perm->has_permission) checked @endif
	@if(isset($permParent) && $permParent->has_permission) disabled @endif>
	{{ $__t($perm->permission_name) }}
</label>
<div id="permission-sub-{{ $perm->permission_name }}">
	<ul>
		@foreach($perm->uihelper_user_permissionsList(array('user_id' => $user->id))->via('parent') as $p)
		<li>
			@include('components.userpermission_select', array(
			'perm' => $p,
			'permParent' => $perm
			))
		</li>
		@endforeach
	</ul>
</div>
