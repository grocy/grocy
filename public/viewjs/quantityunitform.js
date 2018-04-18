$('#save-quantityunit-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/quantity_units', $('#quantityunit-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/quantityunits');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/quantity_units/' + Grocy.EditObjectId, $('#quantityunit-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/quantityunits');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

$('#name').focus();
$('#quantityunit-form').validator();
$('#quantityunit-form').validator('validate');
