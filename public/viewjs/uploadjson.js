$('#upload-json-button').on('click', function(e)
{
	e.preventDefault();

	var redirectDestination = U('/stockoverview');
	var returnTo = GetUriParam('returnto');
	if (returnTo !== undefined)
	{
		redirectDestination = U(returnTo);
	}

	var jsonData = $('#json-form').serializeJSON({ checkboxUncheckedValue: "0" });

	Grocy.FrontendHelpers.BeginUiBusy("json-form");

    Grocy.Api.Post('uploadjson', jsonData,
        function(result)
        {
            window.location.href = redirectDestination
        },
        function (xhr)
        {
            Grocy.FrontendHelpers.EndUiBusy("json-form");
            Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably an error in the json', xhr.response)
        }
    );
});