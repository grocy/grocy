function aboutView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = (selector) => $(scope).find(selector);
	}

	$scope('[data-toggle="collapse-next"]').on("click", function(e)
	{
		e.preventDefault();
		$(this).parent().next().collapse("toggle");
	});

	if ((typeof Grocy.GetUriParam("tab") !== "undefined" && Grocy.GetUriParam("tab") === "changelog"))
	{
		$scope(".nav-tabs a[href='#changelog']").tab("show");
	}

}


window.aboutView = aboutView
