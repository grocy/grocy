$('input.permission-cb').click(
	function()
	{
		check_hierachy(this.checked, this.name);
	}
);

function check_hierachy(checked, name)
{
	var disabled = checked;
	$('#permission-sub-' + name).find('input.permission-cb')
		.prop('checked', disabled)
		.attr('disabled', disabled);
}

$('#permission-save').click(
	function()
	{
		var permission_list = $('input.permission-cb')
			.filter(function()
			{
				return $(this).prop('checked') && !$(this).attr('disabled');
			}).map(function()
			{
				return $(this).data('perm-id');
			}).toArray();
		Grocy.Api.Put('users/' + Grocy.EditObjectId + '/permissions', {
			'permissions': permission_list,
		}, function(result)
		{
			toastr.success(__t("Permissions saved"));
		}, function(xhr)
		{
			toastr.error(__t(JSON.parse(xhr.response).error_message));
		}
		);
	}
);

if (Grocy.EditObjectId == Grocy.UserId)
{
	$('input.permission-cb[name=ADMIN]').click(function()
	{
		if (!this.checked)
		{
			if (!confirm(__t('Are you sure you want to remove full permissions for yourself?')))
			{
				this.checked = true;
				check_hierachy(this.checked, this.name);
			}
		}
	})
}

check_hierachy($("input.permission-cb[name=ADMIN]").is(":checked"), "ADMIN");
