<?php

# Settings can also be overwritten in two ways
#
# First priority
# A .txt file with the same name as the setting in /data/settingoverrides
# the content of the file is used as the setting value
#
# Second priority
# An environment variable with the same name as the setting and prefix "GROCY_"
# so for example "GROCY_BASE_URL"
#
# Third priority
# The settings defined here below


# Either "production", "dev" or "prerelease"
Setting('MODE', 'production');

# Either "en" or "de" or the filename (without extension) of
# one of the other available localization files in the "/localization" directory
Setting('CULTURE', 'en');

# This is used to define the first day of a week for calendar views in the frontend,
# leave empty to use the locale default
# Needs to be a number where Sunday = 0, Monday = 1 and so forth
Setting('CALENDAR_FIRST_DAY_OF_WEEK', '');

# To keep it simple: grocy does not handle any currency conversions,
# this here is used to format all money values,
# so doesn't matter really matter, but should be the
# ISO 4217 code of the currency ("USD", "EUR", "GBP", etc.)
Setting('CURRENCY', 'USD');

# The base url of your installation,
# should be just "/" when running directly under the root of a (sub)domain
# or for example "https://example.com/grocy" when using a subdirectory
Setting('BASE_URL', '/');

# The plugin to use for external barcode lookups,
# must be the filename without .php extension and must be located in /data/plugins,
# see /data/plugins/DemoBarcodeLookupPlugin.php for an example implementation
Setting('STOCK_BARCODE_LOOKUP_PLUGIN', 'DemoBarcodeLookupPlugin');

# If, however, your webserver does not support URL rewriting,
# set this to true
Setting('DISABLE_URL_REWRITING', false);

# Set this to true if you want to disable authentication / the login screen,
# places where user context is needed will then use the default (first existing) user
Setting('DISABLE_AUTH', false);




# Default user settings
# These settings can be changed per user, here the defaults
# are defined which are used when the user has not changed the setting so far

# Night mode related
DefaultUserSetting('night_mode_enabled', false); // If night mode is enabled always
DefaultUserSetting('auto_night_mode_enabled', false); // If night mode is enabled automatically when inside a given time range (see the two settings below)
DefaultUserSetting('auto_night_mode_time_range_from', "20:00"); // Format HH:mm
DefaultUserSetting('auto_night_mode_time_range_to', "07:00"); // Format HH:mm
DefaultUserSetting('auto_night_mode_time_range_goes_over_midnight', true); // If the time range above goes over midnight
DefaultUserSetting('currently_inside_night_mode_range', false); // If we're currently inside of night mode time range (this is not user configurable, but stored as a user setting because it's evaluated client side to be able to use the client time instead of the maybe different server time)

# Stock settings
DefaultUserSetting('product_presets_location_id', -1); // Default location id for new products (-1 means no location is preset)
DefaultUserSetting('product_presets_product_group_id', -1); // Default product group id for new products (-1 means no product group is preset)
DefaultUserSetting('product_presets_qu_id', -1); // Default quantity unit id for new products (-1 means no quantity unit is preset)
DefaultUserSetting('stock_expring_soon_days', 5);
DefaultUserSetting('stock_default_purchase_amount', 0);
DefaultUserSetting('stock_default_consume_amount', 1);

# Chores settings
DefaultUserSetting('chores_due_soon_days', 5);

# Batteries settings
DefaultUserSetting('batteries_due_soon_days', 5);

# Tasks settings
DefaultUserSetting('tasks_due_soon_days', 5);

# If the page should be automatically reloaded when there was
# an external change
DefaultUserSetting('auto_reload_on_db_change', true);

# Show a clock in the header next to the logo or not
DefaultUserSetting('show_clock_in_header', false);

# Shopping list to stock workflow:
# Automatically do the booking using the last price and the amount
# of the shopping list item, if the product has "Default best before days" set
DefaultUserSetting('shopping_list_to_stock_workflow_auto_submit_when_prefilled', false);




# Feature flags
# grocy was initially about "stock management for your household", many other things
# came and still come by, because they are useful - here you can disable the parts
# which you don't need to have a less cluttered UI
# (set the setting to "false" to disable the corresponding part, which should be self explanatory)
Setting('FEATURE_FLAG_STOCK', true);
Setting('FEATURE_FLAG_SHOPPINGLIST', true);
Setting('FEATURE_FLAG_RECIPES', true);
Setting('FEATURE_FLAG_CHORES', true);
Setting('FEATURE_FLAG_TASKS', true);
Setting('FEATURE_FLAG_BATTERIES', true);
Setting('FEATURE_FLAG_EQUIPMENT', true);
Setting('FEATURE_FLAG_CALENDAR', true);
