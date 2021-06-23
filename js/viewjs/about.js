﻿function aboutView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	$scope('[data-toggle="collapse-next"]').on("click", function(e)
	{
		e.preventDefault();
		$(this).parent().next().collapse("toggle");
	});

	if ((typeof GetUriParam("tab") !== "undefined" && GetUriParam("tab") === "changelog"))
	{
		$scope(".nav-tabs a[href='#changelog']").tab("show");
	}

}
