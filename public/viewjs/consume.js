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
					toastr.success(L('Removed #1 #2 of #3 from stock', jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.product.name));

					$('#amount').val(1);
					$('#product_id').val('');
					$('#product_id_text_input').focus();
					$('#product_id_text_input').val('');
					$('#product_id_text_input').trigger('change');
					Grocy.FrontendHelpers.ValidateForm('consume-form');
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
				$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);

				if ((productDetails.stock_amount || 0) === 0)
				{
					$('#product_id').val('');
					$('#product_id_text_input').val('');
					Grocy.FrontendHelpers.ValidateForm('consume-form');
					$('#product-error').text(L('This product is not in stock'));
					$('#product_id_text_input').focus();
				}
				else
				{
					Grocy.FrontendHelpers.ValidateForm('consume-form');
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

$('#consume-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('consume-form');
});

$('#consume-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('consume-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-consume-button').click();
		}
	}
});
