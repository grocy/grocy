$(function ()
{
	const swaggerUi = SwaggerUIBundle({
		url: U('/api/get-open-api-specification'),
		dom_id: '#swagger-ui',
		deepLinking: true,
		presets: [
			SwaggerUIBundle.presets.apis,
			SwaggerUIStandalonePreset
		],
		plugins: [
			SwaggerUIBundle.plugins.DownloadUrl
		],
		layout: 'StandaloneLayout'
	});

	window.ui = swaggerUi;
});
