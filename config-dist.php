<?php

# Login credentials
define('HTTP_USER', 'admin');
define('HTTP_PASSWORD', 'admin');

# Either "production" or "dev"
define('MODE', 'production');

# Either "en" or "de" or the filename (without extension) of
# one of the other available localization files in the "/localization" directory
define('CULTURE', 'en');

# The base url of your installation,
# should be just "/" when running directly under the root of a (sub)domain
# or for example "https:/example.com/grocy" when using a subdirectory
define('BASE_URL', '/');

# The plugin to use for external barcode lookups,
# must be the filename without .php extension and must be located in /data/plugins,
# see /data/plugins/DemoBarcodeLookupPlugin.php for an example implementation
define('STOCK_BARCODE_LOOKUP_PLUGIN', 'DemoBarcodeLookupPlugin');

# If, however, your webserver does not support URL rewriting,
# set this to true
define('DISABLE_URL_REWRITING', false);
