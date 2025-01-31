$("#qu_id").change(function(event)
{
	RefreshQuPluralTestingResult();
});

$("#amount").keyup(function(event)
{
	RefreshQuPluralTestingResult();
});

$("#amount").change(function(event)
{
	RefreshQuPluralTestingResult();
});

function RefreshQuPluralTestingResult()
{
	var singularForm = $("#qu_id option:selected").data("singular-form");
	var pluralForm = $("#qu_id option:selected").data("plural-form");
	var amount = $("#amount").val();

	if (!singularForm || !amount)
	{
		return;
	}

	animateCSS("h2", "flash");
	$("#result").text(__n(amount, singularForm, pluralForm, true));
}

if (GetUriParam("qu") !== undefined)
{
	$("#qu_id").val(GetUriParam("qu"));
	$("#qu_id").trigger("change");
}

setTimeout(function()
{
	$("#amount").focus();
}, Grocy.FormFocusDelay);
