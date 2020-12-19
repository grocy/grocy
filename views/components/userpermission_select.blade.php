<div class="custom-control custom-checkbox">
	<input type="checkbox"
		name="{{ $perm->permission_name }}"
		class="permission-cb form-check-input custom-control-input"
		data-perm-id="{{ $perm->permission_id }}"
		id="perm-{{ $perm->permission_id }}"
		@if($perm->has_permission) checked @endif
	@if(isset($permParent) && $permParent->has_permission) disabled @endif>
	<label class="form-check-label custom-control-label"
		for="perm-{{ $perm->permission_id }}">
		{{ $__t($perm->permission_name) }}
	</label>
</div>
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
