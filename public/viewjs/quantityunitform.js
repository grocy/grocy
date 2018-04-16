$('#save-quantityunit-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.PostJson('/api/add-object/quantity_units', $('#quantityunit-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/quantityunits';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.PostJson('/api/edit-object/quantity_units/' + Grocy.EditObjectId, $('#quantityunit-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/quantityunits';
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
