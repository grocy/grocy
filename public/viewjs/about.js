$('[data-toggle="collapse-next"]').on("click", function(e)
{
	e.preventDefault();
	$(this).parent().next().collapse("toggle");
});

if ((typeof GetUriParam("tab") !== "undefined" && GetUriParam("tab") === "changelog"))
{
	$(".nav-tabs a[href='#changelog']").tab("show");
}
