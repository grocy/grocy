$('[data-toggle="collapse-next"]').on("click", function(e)
{
	e.preventDefault();
	$(this).parent().next().collapse("toggle");
});
