$('#save-location-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.PostJson('/api/add-object/locations', $('#location-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/locations';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.PostJson('/api/edit-object/locations/' + Grocy.EditObjectId, $('#location-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/locations';
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
	$('#location-form').validator();
	$('#location-form').validator('validate');
});
