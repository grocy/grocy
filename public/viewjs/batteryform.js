$('#save-battery-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.PostJson('/api/add-object/batteries', $('#battery-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/batteries';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.PostJson('/api/edit-object/batteries/' + Grocy.EditObjectId, $('#battery-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/batteries';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$(function()
{
	$('#name').focus();
	$('#battery-form').validator();
	$('#battery-form').validator('validate');
});
