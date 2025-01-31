var choresTable = $('#chores-table').DataTable({
	'order': [[1, 'asc']],
	'columnDefs': [
		{ 'orderable': false, 'targets': 0 },
		{ 'searchable': false, "targets": 0 }
	].concat($.fn.dataTable.defaults.columnDefs)
});
$('#chores-table tbody').removeClass("d-none");
choresTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
	var value = $(this).val();
	if (value === "all")
	{
		value = "";
	}

	choresTable.search(value).draw();
}, Grocy.FormFocusDelay));

$("#clear-filter-button").on("click", function()
{
	$("#search").val("");
	choresTable.search("").draw();
	$("#show-disabled").prop('checked', false);
});

$(document).on('click', '.chore-delete-button', function(e)
{
	var objectName = $(e.currentTarget).attr('data-chore-name');
	var objectId = $(e.currentTarget).attr('data-chore-id');

	bootbox.confirm({
		message: __t('Are you sure you want to delete chore "%s"?', objectName),
		closeButton: false,
		buttons: {
			confirm: {
				label: __t('Yes'),
				className: 'btn-success'
			},
			cancel: {
				label: __t('No'),
				className: 'btn-danger'
			}
		},
		callback: function(result)
		{
			if (result === true)
			{
				Grocy.Api.Delete('objects/chores/' + objectId, {},
					function(result)
					{
						window.location.href = U('/chores');
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

$("#show-disabled").change(function()
{
	if (this.checked)
	{
		window.location.href = U('/chores?include_disabled');
	}
	else
	{
		window.location.href = U('/chores');
	}
});

if (GetUriParam('include_disabled'))
{
	$("#show-disabled").prop('checked', true);
}

$(".merge-chores-button").on("click", function(e)
{
	var choreId = $(e.currentTarget).attr("data-chore-id");
	$("#merge-chores-keep").val(choreId);
	$("#merge-chores-remove").val("");
	$("#merge-chores-modal").modal("show");
});

$("#merge-chores-save-button").on("click", function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("merge-chores-form", true))
	{
		return;
	}

	var choreIdToKeep = $("#merge-chores-keep").val();
	var choreIdToRemove = $("#merge-chores-remove").val();

	Grocy.Api.Post("chores/" + choreIdToKeep.toString() + "/merge/" + choreIdToRemove.toString(), {},
		function(result)
		{
			window.location.href = U('/chores');
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while merging', xhr.response);
		}
	);
});
