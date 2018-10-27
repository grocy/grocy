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
					toastr.success(L('Removed #1 #2 of #3 from stock', jsonForm.amount, Pluralize(jsonForm.amount, productDetails.quantity_unit_stock.name, productDetails.quantity_unit_stock.name_plural), productDetails.product.name) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoStockBooking(' + result.booking_id + ')"><i class="fas fa-undo"></i> ' + L("Undo") + '</a>');

					$('#amount').val(1);
					Grocy.Components.ProductPicker.SetValue('');
					Grocy.Components.ProductPicker.GetInputElement().focus();
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

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
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
					Grocy.Components.ProductPicker.SetValue('');
					Grocy.FrontendHelpers.ValidateForm('consume-form');
					Grocy.Components.ProductPicker.ShowCustomError(L('This product is not in stock'));
					Grocy.Components.ProductPicker.GetInputElement().focus();
				}
				else
				{
					Grocy.Components.ProductPicker.HideCustomError();
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

$('#amount').val(1);
Grocy.Components.ProductPicker.GetInputElement().focus();
Grocy.FrontendHelpers.ValidateForm('consume-form');

$('#amount').on('focus', function(e)
{
	$(this).select();
});

$('#consume-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('consume-form');
});

$('#consume-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();
		
		if (document.getElementById('consume-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-consume-button').click();
		}
	}
});

function UndoStockBooking(bookingId)
{
	Grocy.Api.Get('stock/undo-booking/' + bookingId.toString(),
		function(result)
		{
			toastr.success(L("Booking successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
