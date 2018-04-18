$('#save-location-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/locations', $('#location-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/locations');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/locations/' + Grocy.EditObjectId, $('#location-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/locations');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#name').focus();
$('#location-form').validator();
$('#location-form').validator('validate');
