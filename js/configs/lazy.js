function LoadImagesLazy()
{
	$(".lazy").Lazy({
		enableThrottle: true,
		throttle: 500
	});
}

export { LoadImagesLazy }