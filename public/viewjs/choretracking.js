$('.save-choretracking-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("choretracking-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var skipped = $(e.currentTarget).hasClass("skip");

	var jsonForm = $('#choretracking-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("choretracking-form");

	Grocy.Api.Get('chores/' + jsonForm.chore_id,
		function(choreDetails)
		{
			Grocy.Api.Post('chores/' + jsonForm.chore_id + '/execute', { 'tracked_time': Grocy.Components.DateTimePicker.GetValue(), 'done_by': $("#user_id").val(), 'skipped': skipped },
				function(result)
				{
					Grocy.EditObjectId = result.id;
					Grocy.Components.UserfieldsForm.Save(function()
					{
						Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
						toastr.success(__t('Tracked execution of chore %1$s on %2$s', choreDetails.chore.name, Grocy.Components.DateTimePicker.GetValue()) + '<br><a class="btn btn-secondary btn-sm mt-2" href="#" onclick="UndoChoreExecution(' + result.id + ')"><i class="fa-solid fa-undo"></i> ' + __t("Undo") + '</a>');
						Grocy.Components.ChoreCard.Refresh($('#chore_id').val());

						$('#chore_id').val('');
						$('#chore_id_text_input').focus();
						$('#chore_id_text_input').val('');
						Grocy.Components.DateTimePicker.SetValue(moment().format('YYYY-MM-DD HH:mm:ss'));
						$('#chore_id_text_input').trigger('change');
						Grocy.FrontendHelpers.ValidateForm('choretracking-form');
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
					console.error(xhr);
				}
			);
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.EndUiBusy("choretracking-form");
			console.error(xhr);
		}
	);
});

$('#chore_id').on('change', function(e)
{
	var input = $('#chore_id_text_input').val().toString();
	$('#chore_id_text_input').val(input);
	$('#chore_id').data('combobox').refresh();

	var choreId = $(e.target).val();
	if (choreId)
	{
		Grocy.Api.Get('objects/chores/' + choreId,
			function(chore)
			{

				if (chore.track_date_only == 1)
				{
					Grocy.Components.DateTimePicker.ChangeFormat("YYYY-MM-DD");
					Grocy.Components.DateTimePicker.SetValue(moment().format("YYYY-MM-DD"));
				}
				else
				{
					Grocy.Components.DateTimePicker.ChangeFormat("YYYY-MM-DD HH:mm:ss");
					Grocy.Components.DateTimePicker.SetValue(moment().format("YYYY-MM-DD HH:mm:ss"));
				}

				if (chore.period_type == "manually")
				{
					$(".save-choretracking-button.skip").addClass("disabled");
				}
				else
				{
					$(".save-choretracking-button.skip").removeClass("disabled");
				}

				Grocy.FrontendHelpers.ValidateForm('choretracking-form');
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);

		Grocy.Components.ChoreCard.Refresh(choreId);

		setTimeout(function()
		{
			Grocy.Components.DateTimePicker.GetInputElement().focus();
		}, Grocy.FormFocusDelay);

		Grocy.FrontendHelpers.ValidateForm('choretracking-form');
	}
});

$('.combobox').combobox({
	appendId: '_text_input',
	bsVersion: '4',
	clearIfNoMatch: false
});

$('#chore_id_text_input').trigger('change');
Grocy.Components.DateTimePicker.GetInputElement().trigger('input');
Grocy.FrontendHelpers.ValidateForm('choretracking-form');
setTimeout(function()
{
	$('#chore_id_text_input').focus();
}, Grocy.FormFocusDelay);

$('#choretracking-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('choretracking-form');
});

$('#choretracking-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('choretracking-form'))
		{
			return false;
		}
		else
		{
			$('.save-choretracking-button').first().click();
		}
	}
});

$(document).on("Grocy.BarcodeScanned", function(e, barcode, target)
{
	if (!(target == "@chorepicker" || target == "undefined" || target == undefined)) // Default target
	{
		return;
	}

	// Don't know why the blur event does not fire immediately ... this works...
	$("#chore_id_text_input").focusout();
	$("#chore_id_text_input").focus();
	$("#chore_id_text_input").blur();

	$("#chore_id_text_input").val(barcode);

	setTimeout(function()
	{
		$("#chore_id_text_input").focusout();
		$("#chore_id_text_input").focus();
		$("#chore_id_text_input").blur();
		$('#tracked_time').find('input').focus();
	}, Grocy.FormFocusDelay);
});

Grocy.Components.DateTimePicker.GetInputElement().on('keypress', function(e)
{
	Grocy.FrontendHelpers.ValidateForm('choretracking-form');
});

function UndoChoreExecution(executionId)
{
	Grocy.Api.Post('chores/executions/' + executionId.toString() + '/undo', {},
		function(result)
		{
			toastr.success(__t("Chore execution successfully undone"));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};

$('#chore_id_text_input').on('blur', function(e)
{
	if ($('#chore_id').hasClass("combobox-menu-visible"))
	{
		return;
	}

	var input = $('#chore_id_text_input').val().toString();
	var possibleOptionElement = [];

	// Grocycode handling
	if (input.startsWith("grcy"))
	{
		var gc = input.split(":");
		if (gc[1] == "c")
		{
			possibleOptionElement = $("#chore_id option[value=\"" + gc[2] + "\"]").first();
		}

		if (possibleOptionElement.length > 0)
		{
			$('#chore_id').val(possibleOptionElement.val());
			$('#chore_id').data('combobox').refresh();
			$('#chore_id').trigger('change');
		}
		else
		{
			$('#chore_id').val(null);
			$('#chore_id_text_input').val("");
			$('#chore_id').data('combobox').refresh();
			$('#chore_id').trigger('change');
		}
	}
});

$("#tracked_time").find("input").on("focus", function(e)
{
	$(this).select();
});
