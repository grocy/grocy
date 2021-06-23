function choreformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	Grocy.Use("numberpicker");
	var userfieldsform = Grocy.Use("userfieldsform");
	var productPicker = Grocy.Use("productpicker");

	$scope('#save-chore-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#chore-form').serializeJSON();
		if (Grocy.FeatureFlags.GROCY_FEATURE_FLAG_CHORES_ASSIGNMENTS)
		{
			jsonData.assignment_config = $scope("#assignment_config").val().join(",");
		}

		Grocy.FrontendHelpers.BeginUiBusy("chore-form");

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/chores', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfieldsform.Save(function()
					{
						Grocy.Api.Post('chores/executions/calculate-next-assignments', { "chore_id": Grocy.EditObjectId },
							function(result)
							{
								window.location.href = U('/chores');
							},
							function(xhr)
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
					userfieldsform.Save(function()
					{
						Grocy.Api.Post('chores/executions/calculate-next-assignments', { "chore_id": Grocy.EditObjectId },
							function(result)
							{
								window.location.href = U('/chores');
							},
							function(xhr)
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
				$scope('#save-chore-button').click();
			}
		}
	});

	var checkboxValues = $scope("#period_config").val().split(",");
	for (var i = 0; i < checkboxValues.length; i++)
	{
		if (!checkboxValues[i].isEmpty())
		{
			$scope("#" + checkboxValues[i]).prop('checked', true);
		}
	}

	userfieldsform.Load();
	$scope('#name').focus();
	Grocy.FrontendHelpers.ValidateForm('chore-form');

	setTimeout(function()
	{
		$scope(".input-group-chore-period-type").trigger("change");
		$scope(".input-group-chore-assignment-type").trigger("change");

		// Click twice to trigger on-click but not change the actual checked state
		$scope("#consume_product_on_execution").click();
		$scope("#consume_product_on_execution").click();

		productPicker.GetPicker().trigger('change');
	}, 100);

	$scope('.input-group-chore-period-type').on('change', function(e)
	{
		var periodType = $scope('#period_type').val();
		var periodDays = $scope('#period_days').val();
		var periodInterval = $scope('#period_interval').val();

		$scope(".period-type-input").addClass("d-none");
		$scope(".period-type-" + periodType).removeClass("d-none");
		$scope('#chore-period-type-info').attr("data-original-title", "");
		$scope("#period_config").val("");

		if (periodType === 'manually')
		{
			$scope('#chore-period-type-info').attr("data-original-title", __t('This means the next execution of this chore is not scheduled'));
		}
		else if (periodType === 'dynamic-regular')
		{
			$scope("label[for='period_days']").text(__t("Period days"));
			$scope("#period_days").attr("min", "0");
			$scope("#period_days").removeAttr("max");
			$scope('#chore-period-type-info').attr("data-original-title", __t('This means the next execution of this chore is scheduled %s days after the last execution', periodDays.toString()));
		}
		else if (periodType === 'daily')
		{
			$scope('#chore-period-type-info').attr("data-original-title", __t('This means the next execution of this chore is scheduled 1 day after the last execution'));
			$scope('#chore-period-interval-info').attr("data-original-title", __t('This means the next execution of this chore should only be scheduled every %s days', periodInterval.toString()));
		}
		else if (periodType === 'weekly')
		{
			$scope('#chore-period-type-info').attr("data-original-title", __t('This means the next execution of this chore is scheduled 1 day after the last execution, but only for the weekdays selected below'));
			$scope("#period_config").val($(".period-type-weekly input:checkbox:checked").map(function() { return this.value; }).get().join(","));
			$scope('#chore-period-interval-info').attr("data-original-title", __t('This means the next execution of this chore should only be scheduled every %s weeks', periodInterval.toString()));
		}
		else if (periodType === 'monthly')
		{
			$scope('#chore-period-type-info').attr("data-original-title", __t('This means the next execution of this chore is scheduled on the below selected day of each month'));
			$scope("label[for='period_days']").text(__t("Day of month"));
			$scope("#period_days").attr("min", "1");
			$scope("#period_days").attr("max", "31");
			$scope('#chore-period-interval-info').attr("data-original-title", __t('This means the next execution of this chore should only be scheduled every %s months', periodInterval.toString()));
		}
		else if (periodType === 'yearly')
		{
			$scope('#chore-period-type-info').attr("data-original-title", __t('This means the next execution of this chore is scheduled 1 year after the last execution'));
			$scope('#chore-period-interval-info').attr("data-original-title", __t('This means the next execution of this chore should only be scheduled every %s years', periodInterval.toString()));
		}

		Grocy.FrontendHelpers.ValidateForm('chore-form');
	});

	$scope('.input-group-chore-assignment-type').on('change', function(e)
	{
		var assignmentType = $scope('#assignment_type').val();

		$scope('#chore-period-assignment-info').text("");
		$scope("#assignment_config").removeAttr("required");
		$scope("#assignment_config").attr("disabled", "");

		if (assignmentType === 'no-assignment')
		{
			$scope('#chore-assignment-type-info').attr("data-original-title", __t('This means the next execution of this chore will not be assigned to anyone'));
		}
		else if (assignmentType === 'who-least-did-first')
		{
			$scope('#chore-assignment-type-info').attr("data-original-title", __t('This means the next execution of this chore will be assigned to the one who executed it least'));
			$scope("#assignment_config").attr("required", "");
			$scope("#assignment_config").removeAttr("disabled");
		}
		else if (assignmentType === 'random')
		{
			$scope('#chore-assignment-type-info').attr("data-original-title", __t('This means the next execution of this chore will be assigned randomly'));
			$scope("#assignment_config").attr("required", "");
			$scope("#assignment_config").removeAttr("disabled");
		}
		else if (assignmentType === 'in-alphabetical-order')
		{
			$scope('#chore-assignment-type-info').attr("data-original-title", __t('This means the next execution of this chore will be assigned to the next one in alphabetical order'));
			$scope("#assignment_config").attr("required", "");
			$scope("#assignment_config").removeAttr("disabled");
		}

		Grocy.FrontendHelpers.ValidateForm('chore-form', $scope);
	});

	$scope("#consume_product_on_execution").on("click", function()
	{
		if (this.checked)
		{
			productPicker.Enable();
			$scope("#product_amount").removeAttr("disabled");
		}
		else
		{
			productPicker.Disable();
			$scope("#product_amount").attr("disabled", "");
		}

		Grocy.FrontendHelpers.ValidateForm("chore-form");
	});

	productPicker.GetPicker().on('change', function(e)
	{
		var productId = $scope(e.target).val();

		if (productId)
		{
			Grocy.Api.Get('stock/products/' + productId,
				function(productDetails)
				{
					$scope('#amount_qu_unit').text(productDetails.quantity_unit_stock.name);
				},
				function(xhr)
				{
					console.error(xhr);
				}
			);
		}
	});

}


window.choreformView = choreformView
