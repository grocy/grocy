function openapiuiView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	/* global SwaggerUIBundle, SwaggerUIStandalonePreset */
	function HideTopbarPlugin()
	{
		return {
			components: {
				Topbar: function() { return null }
			}
		}
	}
	
	const swaggerUi = SwaggerUIBundle({
		url: Grocy.OpenApi.SpecUrl,
		dom_id: '#swagger-ui',
		deepLinking: true,
		presets: [
			SwaggerUIBundle.presets.apis,
			SwaggerUIStandalonePreset
		],
		plugins: [
			SwaggerUIBundle.plugins.DownloadUrl,
			HideTopbarPlugin
		],
		layout: 'StandaloneLayout',
		docExpansion: "list"
	});
	
	window.ui = swaggerUi;
	
}
