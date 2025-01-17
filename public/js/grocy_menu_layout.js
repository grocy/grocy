$('.navbar-sidenav [data-toggle="tooltip"]').tooltip({
	template: '<div class="tooltip navbar-sidenav-tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
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
		$(".container-fluid").removeClass("pl-md-3");
	}
	else
	{
		window.localStorage.setItem("sidebar_state", "expanded");
		$(".container-fluid").addClass("pl-md-3");
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

// Make sure the current active menu item is visible
var activeMenuItem = $("li.active-page");
if (activeMenuItem.length > 0)
{
	if (!activeMenuItem.isVisibleInViewport(75))
	{
		activeMenuItem[0].scrollIntoView();
	}
}
