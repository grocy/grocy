pushd ..
tx pull --all --minimum-perc=80
tx pull --language en_GB
copy /Y localization\en\userfield_types.po localization\en_GB\userfield_types.po
copy /Y localization\en\stock_transaction_types.po localization\en_GB\stock_transaction_types.po
copy /Y localization\en\component_translations.po localization\en_GB\component_translations.po
copy /Y localization\en\chore_period_types.po localization\en_GB\chore_period_types.po
copy /Y localization\en\chore_assignment_types.po localization\en_GB\chore_assignment_types.po
popd
