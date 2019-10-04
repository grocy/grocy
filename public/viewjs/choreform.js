$('#save-chore-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#chore-form').serializeJSON({ checkboxUncheckedValue: "0" });
	if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
	{
		jsonData.assignment_config = $("#assignment_config").val().join(",");
	}
	
	Grocy.FrontendHelpers.BeginUiBusy("chore-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/chores', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					Grocy.Api.Post('chores/executions/calculate-next-assignments', { "chore_id": Grocy.EditObjectId },
						function (result)
						{
							window.location.href = U('/chores');
						},
						function (xhr)
						{
							Grocy.FrontendHelpers.EndUiBusy();
							console.error(xhr);
						}
					);
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
					Grocy.Api.Post('chores/executions/calculate-next-assignments', { "chore_id": Grocy.EditObjectId },
						function (result)
						{
							window.location.href = U('/chores');
						},
						function (xhr)
						{
							Grocy.FrontendHelpers.EndUiBusy();
							console.error(xhr);
						}
					);
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
	$(".input-group-chore-assignment-type").trigger("change");

	// Click twice to trigger on-click but not change the actual checked state
	$("#consume_product_on_execution").click();
	$("#consume_product_on_execution").click();

	Grocy.Components.ProductPicker.GetPicker().trigger('change');
}, 100);

$('.input-group-chore-period-type').on('change', function(e)
{
	var periodType = $('#period_type').val();
	var periodDays = $('#period_days').val();
	var periodInterval = $('#period_interval').val();

	$(".period-type-input").addClass("d-none");
	$(".period-type-" + periodType).removeClass("d-none");
	$('#chore-period-type-info').text("");
	$("#period_config").val("");

	if (periodType === 'manually')
	{
		$('#chore-period-type-info').text(__t('This means the next execution of this chore is not scheduled'));
	}
	else if (periodType === 'dynamic-regular')
	{
		$("label[for='period_days']").text(__t("Period days"));
		$("#period_days").attr("min", "0");
		$("#period_days").attr("max", "9999");
		$("#period_days").parent().find(".invalid-feedback").text(__t('This cannot be negative'));
		$('#chore-period-type-info').text(__t('This means the next execution of this chore is scheduled %s days after the last execution', periodDays.toString()));
	}
	else if (periodType === 'daily')
	{
		$('#chore-period-type-info').text(__t('This means the next execution of this chore is scheduled 1 day after the last execution'));
		$('#chore-period-interval-info').text(__t('This means the next execution of this chore should only be scheduled every %s days', periodInterval.toString()));
	}
	else if (periodType === 'weekly')
	{
		$('#chore-period-type-info').text(__t('This means the next execution of this chore is scheduled 1 day after the last execution, but only for the weekdays selected below'));
		$("#period_config").val($(".period-type-weekly input:checkbox:checked").map(function () { return this.value; }).get().join(","));
		$('#chore-period-interval-info').text(__t('This means the next execution of this chore should only be scheduled every %s weeks', periodInterval.toString()));
	}
	else if (periodType === 'monthly')
	{
		$('#chore-period-type-info').text(__t('This means the next execution of this chore is scheduled on the below selected day of each month'));
		$("label[for='period_days']").text(__t("Day of month"));
		$("#period_days").attr("min", "1");
		$("#period_days").attr("max", "31");
		$("#period_days").parent().find(".invalid-feedback").text(__t('The amount must be between %1$s and %2$s', "1", "31"));
		$('#chore-period-interval-info').text(__t('This means the next execution of this chore should only be scheduled every %s months', periodInterval.toString()));
	}
	else if (periodType === 'yearly')
	{
		$('#chore-period-type-info').text(__t('This means the next execution of this chore is scheduled 1 year after the last execution'));
		$('#chore-period-interval-info').text(__t('This means the next execution of this chore should only be scheduled every %s years', periodInterval.toString()));
	}

	Grocy.FrontendHelpers.ValidateForm('chore-form');
});

$('.input-group-chore-assignment-type').on('change', function(e)
{
	var assignmentType = $('#assignment_type').val();

	$('#chore-period-assignment-info').text("");
	$("#assignment_config").removeAttr("required");
	$("#assignment_config").attr("disabled", "");

	if (assignmentType === 'no-assignment')
	{
		$('#chore-assignment-type-info').text(__t('This means the next execution of this chore will not be assigned to anyone'));
	}
	else if (assignmentType === 'who-least-did-first')
	{
		$('#chore-assignment-type-info').text(__t('This means the next execution of this chore will be assigned to the one who executed it least'));
		$("#assignment_config").attr("required", "");
		$("#assignment_config").removeAttr("disabled");
	}
	else if (assignmentType === 'random')
	{
		$('#chore-assignment-type-info').text(__t('This means the next execution of this chore will be assigned randomly'));
		$("#assignment_config").attr("required", "");
		$("#assignment_config").removeAttr("disabled");
	}
	else if (assignmentType === 'in-alphabetical-order')
	{
		$('#chore-assignment-type-info').text(__t('This means the next execution of this chore will be assigned to the next one in alphabetical order'));
		$("#assignment_config").attr("required", "");
		$("#assignment_config").removeAttr("disabled");
	}

	Grocy.FrontendHelpers.ValidateForm('chore-form');
});

$("#consume_product_on_execution").on("click", function()
{
	if (this.checked)
	{
		Grocy.Components.ProductPicker.Enable();
		$("#product_amount").removeAttr("disabled");
	}
	else
	{
		Grocy.Components.ProductPicker.Disable();
		$("#product_amount").attr("disabled", "");
	}

	Grocy.FrontendHelpers.ValidateForm("chore-form");
});

Grocy.Components.ProductPicker.GetPicker().on('change', function(e)
{
	var productId = $(e.target).val();

	if (productId)
	{
		Grocy.Api.Get('stock/products/' + productId,
			function(productDetails)
			{
				$('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
});
