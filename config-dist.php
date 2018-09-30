<?php

# Either "production", "dev" or "prerelease"
Setting('MODE', 'production');

# Either "en" or "de" or the filename (without extension) of
# one of the other available localization files in the "/localization" directory
Setting('CULTURE', 'en');

# To keep it simple: grocy does not handle any currency conversions,
# this here is used to format all money values,
# so can be anything (e. g. "USD" OR "$", doesn't matter...)
Setting('CURRENCY', '$');

# The base url of your installation,
# should be just "/" when running directly under the root of a (sub)domain
# or for example "https:/example.com/grocy" when using a subdirectory
Setting('BASE_URL', '/');

# The plugin to use for external barcode lookups,
# must be the filename without .php extension and must be located in /data/plugins,
# see /data/plugins/DemoBarcodeLookupPlugin.php for an example implementation
Setting('STOCK_BARCODE_LOOKUP_PLUGIN', 'DemoBarcodeLookupPlugin');

# If, however, your webserver does not support URL rewriting,
# set this to true
Setting('DISABLE_URL_REWRITING', false);


# Default user settings
# These settings can be changed per user, here the defaults
# are defined which are used when the user has not changed the setting so far

# Night mode related
DefaultUserSetting('night_mode_enabled', false); // If night mode is enabled always
DefaultUserSetting('auto_night_mode_enabled', false); // If night mode is enabled automatically when inside a given time range (see the two settings below)
DefaultUserSetting('auto_night_mode_time_range_from', "20:00"); // Format HH:mm
DefaultUserSetting('auto_night_mode_time_range_to', "07:00"); // Format HH:mm

# If the page should be automatically reloaded when there was
# an external change
DefaultUserSetting('auto_reload_on_db_change', true);
