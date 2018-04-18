$('#save-battery-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/batteries', $('#battery-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/batteries');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/batteries/' + Grocy.EditObjectId, $('#battery-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/batteries');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#name').focus();
$('#battery-form').validator();
$('#battery-form').validator('validate');
