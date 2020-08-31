$('#locale-save').on('click', function () {
	var value = $("input:radio[name ='language']:checked").val();
	var jsonData = {'value': value};
	Grocy.Api.Put('user/settings/locale', jsonData,
		function(result)
		{
			location.pathname = GetUriParam('returnto');
		},
		function(xhr)
		{
			if (!xhr.statusText.isEmpty())
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		}
	);
});
