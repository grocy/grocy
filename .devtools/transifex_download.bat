pushd ..

for /d %%d in (localization\*) do (
	if %%~nxd neq en (
		tx pull --languages %%~nxd --force
	)
)

copy /Y localization\en\userfield_types.po localization\en_GB\userfield_types.po
copy /Y localization\en\stock_transaction_types.po localization\en_GB\stock_transaction_types.po
copy /Y localization\en\component_translations.po localization\en_GB\component_translations.po
copy /Y localization\en\chore_period_types.po localization\en_GB\chore_period_types.po
copy /Y localization\en\chore_assignment_types.po localization\en_GB\chore_assignment_types.po
copy /Y localization\en\permissions.po localization\en_GB\permissions.po
copy /Y localization\en\locales.po localization\en_GB\locales.po

popd
