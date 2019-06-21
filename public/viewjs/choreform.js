$('#save-chore-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#chore-form').serializeJSON({ checkboxUncheckedValue: "0" });
	Grocy.FrontendHelpers.BeginUiBusy("chore-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/chores', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/chores');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("chore-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/chores/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/chores');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("chore-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#chore-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('chore-form');
});

$('#chore-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('chore-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-chore-button').click();
		}
	}
});

var checkboxValues = $("#period_config").val().split(",");
for (var i = 0; i < checkboxValues.length; i++)
{
	if (!checkboxValues[i].isEmpty())
	{
		$("#" + checkboxValues[i]).prop('checked', true);
	}
}

Grocy.Components.UserfieldsForm.Load();
$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('chore-form');

setTimeout(function()
{
	$(".input-group-chore-period-type").trigger("change");
}, 100);

$('.input-group-chore-period-type').on('change', function(e)
{
	var periodType = $('#period_type').val();
	var periodDays = $('#period_days').val();

	$(".period-type-input").addClass("d-none");
	$(".period-type-" + periodType).removeClass("d-none");
	$('#chore-period-type-info').text("");
	$("#period_config").val("");

	if (periodType === 'manually')
	{
		//
	}
	else if (periodType === 'dynamic-regular')
	{
		$("label[for='period_days']").text(__t("Period days"));
		$("#period_days").attr("min", "0");
		$("#period_days").attr("max", "9999");
		$("#period_days").parent().find(".invalid-feedback").text(__t('This cannot be negative'));
		$('#chore-period-type-info').text(__t('This means it is estimated that a new execution of this chore is tracked %s days after the last was tracked', periodDays.toString()));
	}
	else if (periodType === 'daily')
	{
		//
	}
	else if (periodType === 'weekly')
	{
		$("#period_config").val($(".period-type-weekly input:checkbox:checked").map(function () { return this.value; }).get().join(","));
	}
	else if (periodType === 'monthly')
	{
		$("label[for='period_days']").text(__t("Day of month"));
		$("#period_days").attr("min", "1");
		$("#period_days").attr("max", "31");
		$("#period_days").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', "1", "31"));
	}
});
