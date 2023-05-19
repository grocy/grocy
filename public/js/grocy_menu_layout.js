$('.navbar-sidenav [data-toggle="tooltip"]').tooltip({
	template: '<div class="tooltip navbar-sidenav-tooltip" role="tooltip" style="pointer-events: none;"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
})

$("#sidenavToggler").click(function(e)
{
	e.preventDefault();
	$("body").toggleClass("sidenav-toggled");
	$(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
	$(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
});

$(".navbar-sidenav .nav-link-collapse").click(function(e)
{
	e.preventDefault();
	$("body").removeClass("sidenav-toggled");
});


