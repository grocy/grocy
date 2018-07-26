<?php

# Either "production" or "dev"
Setting('MODE', 'production');

# Either "en" or "de" or the filename (without extension) of
# one of the other available localization files in the "/localization" directory
Setting('CULTURE', 'en');

# To keep it simpel, grocy does not handle any currency conversions,
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
