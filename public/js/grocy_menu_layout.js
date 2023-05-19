$('.navbar-sidenav [data-toggle="tooltip"]').tooltip({
	template: '<div class="tooltip navbar-sidenav-tooltip" role="tooltip" style="pointer-events: none;"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
})

$("#sidenavToggler").click(function(e)
{
	e.preventDefault();
	$("body").toggleClass("sidenav-toggled");
	$(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
	$(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");

	if ($("body").hasClass("sidenav-toggled"))
	{
		window.localStorage.setItem("sidebar_state", "collapsed");
	}
	else
	{
		window.localStorage.setItem("sidebar_state", "expanded");
	}
});

$(".navbar-sidenav .nav-link-collapse").click(function(e)
{
	e.preventDefault();
	$("body").removeClass("sidenav-toggled");
	window.localStorage.setItem("sidebar_state", "expanded");
});

if (window.localStorage.getItem("sidebar_state") === "collapsed")
{
	$("#sidenavToggler").click();
}

if (Grocy.ActiveNav)
{
	var menuItem = $('#sidebarResponsive').find("[data-nav-for-page='" + Grocy.ActiveNav + "']");
	if (menuItem)
	{
		menuItem.addClass('active-page');

		var parentMenuSelector = menuItem.data("sub-menu-of");
		if (parentMenuSelector)
		{
			$(parentMenuSelector).collapse("show");
			$(parentMenuSelector).prev(".nav-link-collapse").addClass("active-page");

			$(parentMenuSelector).on("shown.bs.collapse", function(e)
			{
				if (!menuItem.isVisibleInViewport(75))
				{
					menuItem[0].scrollIntoView();
				}
			})
		}
		else
		{
			if (!menuItem.isVisibleInViewport(75))
			{
				menuItem[0].scrollIntoView();
			}
		}
	}
}
