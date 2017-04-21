$('#save-shoppinglist-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.PostJson('/api/add-object/shopping_list', $('#shoppinglist-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/shoppinglist';
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
	else
	{
		Grocy.PostJson('/api/edit-object/shopping_list/' + Grocy.EditObjectId, $('#shoppinglist-form').serializeJSON(),
			function(result)
			{
				window.location.href = '/shoppinglist';
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
	$('.combobox').combobox({
		appendId: '_text_input'
	});

	$('#product_id_text_input').on('change', function(e)
	{
		var input = $('#product_id_text_input').val().toString();
		var possibleOptionElement = $("#product_id option[data-additional-searchdata*='" + input + "']").first();
		
		if (possibleOptionElement.length > 0)
		{
			$('#product_id').val(possibleOptionElement.val());
			$('#product_id').data('combobox').refresh();
			$('#product_id').trigger('change');
		}
	});

	if (Grocy.EditMode === 'create')
	{
		$('#product_id').val('');
		$('#product_id_text_input').focus();
		$('#product_id_text_input').val('');
		$('#product_id_text_input').trigger('change');
	}
	else
	{
		$('#product_id').data('combobox').refresh();
		$('#product_id').trigger('change');
	}

	$('#shoppinglist-form').validator();
	$('#shoppinglist-form').validator('validate');

	$('#amount').on('focus', function(e)
	{
		if ($('#product_id_text_input').val().length === 0)
		{
			$('#product_id_text_input').focus();
		}
	});

	$('#shoppinglist-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			if ($('#shoppinglist-form').validator('validate').has('.has-error').length !== 0) //There is at least one validation error
			{
				event.preventDefault();
				return false;
			}
		}
	});
});
