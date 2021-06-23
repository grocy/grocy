function quantityunitpluraltestingView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");

	$scope("#qu_id").change(function(event)
	{
		RefreshQuPluralTestingResult();
	});

	$scope("#amount").keyup(function(event)
	{
		RefreshQuPluralTestingResult();
	});

	$scope("#amount").change(function(event)
	{
		RefreshQuPluralTestingResult();
	});

	function RefreshQuPluralTestingResult()
	{
		var singularForm = $scope("#qu_id option:selected").data("singular-form");
		var pluralForm = $scope("#qu_id option:selected").data("plural-form");
		var amount = $scope("#amount").val();

		if (singularForm.toString().isEmpty() || amount.toString().isEmpty())
		{
			return;
		}

		animateCSS("h2", "shake");
		$scope("#result").text(__n(amount, singularForm, pluralForm));
	}

	if (Grocy.GetUriParam("qu") !== undefined)
	{
		$scope("#qu_id").val(Grocy.GetUriParam("qu"));
		$scope("#qu_id").trigger("change");
	}

	$scope("#amount").focus();

}
