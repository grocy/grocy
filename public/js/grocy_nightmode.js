$("#night-mode-enabled").on("change", function()
{
	var value = $(this).is(":checked");
	window.localStorage.setItem("night_mode", value);

	if (value)
	{
		$("body").addClass("night-mode");
	}
	else
	{
		$("body").removeClass("night-mode");
	}
});

if (window.localStorage.getItem("night_mode") === "true")
{
	$("body").addClass("night-mode");
	$("#night-mode-enabled").prop("checked", true);
}
