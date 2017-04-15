$(document).on('click', '.location-delete-button', function(e)
{
	bootbox.confirm({
		message: 'Delete location <strong>' + $(e.target).attr('data-location-name') + '</strong>?',
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result == true)
			{
				Grocy.FetchJson('/api/delete-object/locations/' + $(e.target).attr('data-location-id'),
					function(result)
					{
						window.location.href = '/locations';
					},
					function(xhr)
					{
						console.error(xhr);
					}
				);
			}
		}
	});
});
