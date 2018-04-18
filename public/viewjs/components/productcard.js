Grocy.Components.ProductCard = { };

Grocy.Components.ProductCard.Refresh = function(productId)
{
	Grocy.Api.Get('stock/get-product-details/' + productId,
		function(productDetails)
		{
			$('#productcard-product-name').text(productDetails.product.name);
			$('#productcard-product-stock-amount').text(productDetails.stock_amount || '0');
			$('#productcard-product-stock-qu-name').text(productDetails.quantity_unit_stock.name);
			$('#productcard-product-stock-qu-name2').text(productDetails.quantity_unit_stock.name);
			$('#productcard-product-last-purchased').text((productDetails.last_purchased || L('never')).substring(0, 10));
			$('#productcard-product-last-purchased-timeago').text($.timeago(productDetails.last_purchased || ''));
			$('#productcard-product-last-used').text((productDetails.last_used || L('never')).substring(0, 10));
			$('#productcard-product-last-used-timeago').text($.timeago(productDetails.last_used || ''));

			EmptyElementWhenMatches('#productcard-product-last-purchased-timeago', L('timeago_nan'));
			EmptyElementWhenMatches('#productcard-product-last-used-timeago', L('timeago_nan'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
