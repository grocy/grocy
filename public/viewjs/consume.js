$('#save-consume-button').on('click', function(e)
{
	e.preventDefault();

	var jsonForm = $('#consume-form').serializeJSON();

	var spoiled = 0;
	if ($('#spoiled').is(':checked'))
	{
		spoiled = 1;
	}

	Grocy.Api.Get('stock/get-product-details/' + jsonForm.product_id,
		function (productDetails)
		{
			Grocy.Api.Get('stock/consume-product/' + jsonForm.product_id + '/' + jsonForm.amount + '?spoiled=' + spoiled,
				function(result)
				{
					toastr.success('Removed ' + jsonForm.amount + ' ' + productDetails.quantity_unit_stock.name + ' of ' + productDetails.product.name + ' from stock');

					$('#amount').val(1);
					$('#product_id').val('');
					$('#product_id_text_input').focus();
					$('#product_id_text_input').val('');
					$('#product_id_text_input').trigger('change');
					$('#consume-form').validator('validate');
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
});

$('#product_id').on('change', function(e)
{
	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Components.ProductCard.Refresh(productId);

		Grocy.Api.Get('stock/get-product-details/' + productId,
			function (productDetails)
			{
				$('#amount').attr('max', productDetails.stock_amount);
				$('#consume-form').validator('update');
				$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);

				if ((productDetails.stock_amount || 0) === 0)
				{
					$('#product_id').val('');
					$('#product_id_text_input').val('');
					$('#product_id_text_input').addClass('has-error');
					$('#product_id_text_input').parent('.input-group').addClass('has-error');
					$('#product_id_text_input').closest('.form-group').addClass('has-error');
					$('#product-error').text('This product is not in stock.');
					$('#product-error').show();
					$('#product_id_text_input').focus();
				}
				else
				{
					$('#product_id_text_input').removeClass('has-error');
					$('#product_id_text_input').parent('.input-group').removeClass('has-error');
					$('#product_id_text_input').closest('.form-group').removeClass('has-error');
					$('#product-error').hide();
					$('#amount').focus();
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});

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

$('#amount').val(1);
$('#product_id').val('');
$('#product_id_text_input').focus();
$('#product_id_text_input').val('');
$('#product_id_text_input').trigger('change');

$('#consume-form').validator();
$('#consume-form').validator('validate');

$('#amount').on('focus', function(e)
{
	if ($('#product_id_text_input').val().length === 0)
	{
		$('#product_id_text_input').focus();
	}
	else
	{
		$(this).select();
	}
});

$('#consume-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if ($('#consume-form').validator('validate').has('.has-error').length !== 0) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
	}
});
