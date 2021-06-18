
function setPermissions(permissions)
{
	for (let item of permissions)
	{
		if (item.has_permission == 0)
		{
			$('.permission-' + item.permission_name).addClass('disabled').addClass('not-allowed');
		}
	}
}