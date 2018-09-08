$('#save-product-button').on('click', function(e)
{
	e.preventDefault();

	var redirectDestination = U('/products');
	var returnTo = GetUriParam('returnto');
	if (returnTo !== undefined)
	{
		redirectDestination = returnTo + '?createdproduct=' + encodeURIComponent($('#name').val());
	}

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/products', $('#product-form').serializeJSON(),
			function(result)
			{
				window.location.href = redirectDestination;
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/products/' + Grocy.EditObjectId, $('#product-form').serializeJSON(),
			function(result)
			{
				window.location.href = redirectDestination;
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#barcode-taginput').tagsManager({
	'hiddenTagListName': 'barcode',
	'tagsContainer': '#barcode-taginput-container'
});

if (Grocy.EditMode === 'edit')
{
	Grocy.Api.Get('get-object/products/' + Grocy.EditObjectId,
		function (product)
		{
			if (product.barcode !== null && product.barcode.length > 0)
			{
				product.barcode.split(',').forEach(function(item)
				{
					$('#barcode-taginput').tagsManager('pushTag', item);
				});
			}
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}

var prefillName = GetUriParam('prefillname');
if (prefillName !== undefined)
{
	$('#name').val(prefillName);
	$('#name').focus();
}

var prefillBarcode = GetUriParam('prefillbarcode');
if (prefillBarcode !== undefined)
{
	$('#barcode-taginput').tagsManager('pushTag', prefillBarcode);
	$('#name').focus();
}

$('.input-group-qu').on('change', function(e)
{
	var factor = $('#qu_factor_purchase_to_stock').val();
	if (factor > 1)
	{
		$('#qu-conversion-info').text(L('This means 1 #1 purchased will be converted into #2 #3 in stock', $("#qu_id_purchase option:selected").text(), (1 * factor).toString(), $("#qu_id_stock option:selected").text()));
		$('#qu-conversion-info').removeClass('d-none');
	}
	else
	{
		$('#qu-conversion-info').addClass('d-none');
	}
});

$('#product-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('product-form');
});

$('#product-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('product-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-product-button').click();
		}
	}
});

$('#name').focus();
$('.input-group-qu').trigger('change');
Grocy.FrontendHelpers.ValidateForm('product-form');
