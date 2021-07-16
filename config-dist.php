<?php

// Settings can also be overwritten in two ways
//
// First priority
// A .txt file with the same name as the setting in /data/settingoverrides
// the content of the file is used as the setting value
//
// Second priority
// An environment variable with the same name as the setting and prefix "GROCY_"
// so for example "GROCY_BASE_URL"
//
// Third priority
// The settings defined here below

// Either "production", "dev", "demo" or "prerelease"
// When not "production", authentication will be disabled and
// demo data will be populated during database migrations
Setting('MODE', 'production');

// Either "en" or "de" or the directory name of
// one of the other available localization folders in the "/localization" directory
Setting('DEFAULT_LOCALE', 'en');

// This is used to define the first day of a week for calendar views in the frontend,
// leave empty to use the locale default
// Needs to be a number where Sunday = 0, Monday = 1 and so forth
Setting('CALENDAR_FIRST_DAY_OF_WEEK', '');

// If calendars should show week numbers
Setting('CALENDAR_SHOW_WEEK_OF_YEAR', true);

// To keep it simple: grocy does not handle any currency conversions,
// this here is used to format all money values,
// so doesn't really matter, but should be the
// ISO 4217 code of the currency ("USD", "EUR", "GBP", etc.)
Setting('CURRENCY', 'USD');

// When running grocy in a subdirectory, this should be set to the relative path, otherwise empty
// It needs to be set to the part (of the URL) after the document root,
// if URL rewriting is disabled, including index.php
// Example with URL Rewriting support:
//  Root URL = https://example.com/grocy
//  => BASE_PATH = /grocy
// Example without URL Rewriting support:
//  Root URL = https://example.com/grocy/public/index.php/
//  => BASE_PATH = /grocy/public/index.php
Setting('BASE_PATH', '');

// The base URL of your installation,
// should be just "/" when running directly under the root of a (sub)domain
// or for example "https://example.com/grocy" when using a subdirectory
Setting('BASE_URL', '/');

// The plugin to use for external barcode lookups,
// must be the filename without .php extension and must be located in /data/plugins,
// see /data/plugins/DemoBarcodeLookupPlugin.php for an example implementation
Setting('STOCK_BARCODE_LOOKUP_PLUGIN', 'DemoBarcodeLookupPlugin');

// If, however, your webserver does not support URL rewriting, set this to true
Setting('DISABLE_URL_REWRITING', false);

// Specify an custom homepage if desired - by default the homepage will be set to the stock overview page,
// this needs to be one of the following values:
// stock, shoppinglist, recipes, chores, tasks, batteries, equipment, calendar, mealplan
Setting('ENTRY_PAGE', 'stock');

// Set this to true if you want to disable authentication / the login screen,
// places where user context is needed will then use the default (first existing) user
Setting('DISABLE_AUTH', false);

// Either "Grocy\Middleware\DefaultAuthMiddleware", "Grocy\Middleware\ReverseProxyAuthMiddleware"
// or any class that implements Grocy\Middleware\AuthMiddleware
Setting('AUTH_CLASS', 'Grocy\Middleware\DefaultAuthMiddleware');

// When using ReverseProxyAuthMiddleware,
// the name of the HTTP header which your reverse proxy uses to pass the username (on successful authentication)
Setting('REVERSE_PROXY_AUTH_HEADER', 'REMOTE_USER');

// LDAP options when using LdapAuthMiddleware
Setting('LDAP_ADDRESS', ''); // Example value "ldap://vm-dc2019.local.berrnd.net"
Setting('LDAP_BASE_DN', ''); // Example value "DC=local,DC=berrnd,DC=net"
Setting('LDAP_BIND_DN', ''); // Example value "CN=grocy_bind_account,OU=service_accounts,DC=local,DC=berrnd,DC=net"
Setting('LDAP_BIND_PW', ''); // Password for the above account
Setting('LDAP_USER_FILTER', ''); // Example value "(OU=grocy_users)"
Setting('LDAP_UID_ATTR', ''); // Windows AD: "sAMAccountName", OpenLDAP: "uid", GLAuth: "cn"

// Set this to true if you want to disable the ability to scan a barcode via the device camera (Browser API)
Setting('DISABLE_BROWSER_BARCODE_CAMERA_SCANNING', false);

// Set this if you want to have a different start day for the weekly meal plan view,
// leave empty to use CALENDAR_FIRST_DAY_OF_WEEK (see above)
// Needs to be a number where Sunday = 0, Monday = 1 and so forth
Setting('MEAL_PLAN_FIRST_DAY_OF_WEEK', '');

// Default permissions for new users
// the array needs to contain the technical/constant names
// see the file controllers/Users/User.php for possible values
Setting('DEFAULT_PERMISSIONS', ['ADMIN']);

// 1D (=> Code128) or 2D (=> DataMatrix)
Setting('GROCYCODE_TYPE', '1D');

// Label printer settings
// This is the URI that grocy will POST to when asked to print a label
Setting('LABEL_PRINTER_WEBHOOK', '');
// This setting decides whether the webhook will be called server- or clientside
// If the machine grocy runs on has a network connection to the host the webhook receiver is on, this is probably a good idea
// If, for example, grocy runs in the cloud and your printer daemon runs locally to you, set this to false to let your browser call the webhook instead
Setting('LABEL_PRINTER_RUN_SERVER', true);
// Additional parameters supplied to the webhook
Setting('LABEL_PRINTER_PARAMS', ['font_family' => 'Source Sans Pro (Regular)']);
// TRUE to use JSON or FALSE to use normal POST request variables
Setting('LABEL_PRINTER_HOOK_JSON', false);

// Thermal printer options
// Thermal printers are receipt printers, not regular printers,
// the printer must support the ESC/POS protocol, see https://github.com/mike42/escpos-php
Setting('TPRINTER_IS_NETWORK_PRINTER', false); // Set to true if it's' a network printer
Setting('TPRINTER_PRINT_QUANTITY_NAME', true); // Set to false if you do not want to print the quantity names (related to the shopping list)
Setting('TPRINTER_PRINT_NOTES', true); // Set to false if you do not want to print notes (related to the shopping list)
Setting('TPRINTER_IP', '127.0.0.1'); // IP of the network printer (does only matter if it's a network printer)
Setting('TPRINTER_PORT', 9100); // Port of the network printer
Setting('TPRINTER_CONNECTOR', '/dev/usb/lp0'); // Printer device (does only matter if you use a locally attached printer)
// For USB on Linux this is often '/dev/usb/lp0', for serial printers it could be similar to '/dev/ttyS0'
// Make sure that the user that runs the webserver has permissions to write to the printer - on Linux add your webserver user to the LP group with usermod -a -G lp www-data


// Default user settings
// These settings can be changed per user, here the defaults
// are defined which are used when the user has not changed the setting so far

// Night mode related
DefaultUserSetting('night_mode_enabled', false); // If night mode is enabled always
DefaultUserSetting('auto_night_mode_enabled', false); // If night mode is enabled automatically when inside a given time range (see the two settings below)
DefaultUserSetting('auto_night_mode_time_range_from', '20:00'); // Format HH:mm
DefaultUserSetting('auto_night_mode_time_range_to', '07:00'); // Format HH:mm
DefaultUserSetting('auto_night_mode_time_range_goes_over_midnight', true); // If the time range above goes over midnight
DefaultUserSetting('currently_inside_night_mode_range', false); // If we're currently inside of night mode time range (this is not user configurable, but stored as a user setting because it's evaluated client side to be able to use the client time instead of the maybe different server time)

// Keep screen on settings
DefaultUserSetting('keep_screen_on', false); // Keep the screen always on
DefaultUserSetting('keep_screen_on_when_fullscreen_card', false); // Keep the screen on when a "fullscreen-card" is displayed

// Stock settings
DefaultUserSetting('product_presets_location_id', -1); // Default location id for new products (-1 means no location is preset)
DefaultUserSetting('product_presets_product_group_id', -1); // Default product group id for new products (-1 means no product group is preset)
DefaultUserSetting('product_presets_qu_id', -1); // Default quantity unit id for new products (-1 means no quantity unit is preset)
DefaultUserSetting('stock_decimal_places_amounts', 4); // Default decimal places allowed for amounts
DefaultUserSetting('stock_decimal_places_prices', 2); // Default decimal places allowed for prices
DefaultUserSetting('stock_auto_decimal_separator_prices', false);
DefaultUserSetting('stock_due_soon_days', 5);
DefaultUserSetting('stock_default_purchase_amount', 0);
DefaultUserSetting('stock_default_consume_amount', 1);
DefaultUserSetting('stock_default_consume_amount_use_quick_consume_amount', false);
DefaultUserSetting('scan_mode_consume_enabled', false);
DefaultUserSetting('scan_mode_purchase_enabled', false);
DefaultUserSetting('show_icon_on_stock_overview_page_when_product_is_on_shopping_list', true);
DefaultUserSetting('show_purchased_date_on_purchase', false); // Whether the purchased date should be editable on purchase (defaults to today otherwise)
DefaultUserSetting('show_warning_on_purchase_when_due_date_is_earlier_than_next', true); // Show a warning on purchase when the due date of the purchased product is earlier than the next due date in stock

// Shopping list settings
DefaultUserSetting('shopping_list_to_stock_workflow_auto_submit_when_prefilled', false); // Automatically do the booking using the last price and the amount of the shopping list item, if the product has "Default due days" set
DefaultUserSetting('shopping_list_show_calendar', false);

// Recipe settings
DefaultUserSetting('recipe_ingredients_group_by_product_group', false); // Group recipe ingredients by their product group

// Chores settings
DefaultUserSetting('chores_due_soon_days', 5);

// Batteries settings
DefaultUserSetting('batteries_due_soon_days', 5);

// Tasks settings
DefaultUserSetting('tasks_due_soon_days', 5);

// If the page should be automatically reloaded when there was an external change
DefaultUserSetting('auto_reload_on_db_change', false);

// Show a clock in the header next to the logo or not
DefaultUserSetting('show_clock_in_header', false);

// Component configuration for Quagga2 - read https://github.com/ericblade/quagga2#configobject for details
// Below is a generic good configuration,
// for an iPhone 7 Plus, halfsample = true, patchsize = small, frequency = 5 yields very good results
DefaultUserSetting('quagga2_numofworkers', 4);
DefaultUserSetting('quagga2_halfsample', false);
DefaultUserSetting('quagga2_patchsize', 'medium');
DefaultUserSetting('quagga2_frequency', 10);
DefaultUserSetting('quagga2_debug', true);


// Feature flags
// grocy was initially about "stock management for your household", many other things
// came and still come by, because they are useful - here you can disable the parts
// which you don't need to have a less cluttered UI
// (set the setting to "false" to disable the corresponding part, which should be self explanatory)
Setting('FEATURE_FLAG_STOCK', true);
Setting('FEATURE_FLAG_SHOPPINGLIST', true);
Setting('FEATURE_FLAG_RECIPES', true);
Setting('FEATURE_FLAG_CHORES', true);
Setting('FEATURE_FLAG_TASKS', true);
Setting('FEATURE_FLAG_BATTERIES', true);
Setting('FEATURE_FLAG_EQUIPMENT', true);
Setting('FEATURE_FLAG_CALENDAR', true);
Setting('FEATURE_FLAG_LABEL_PRINTER', false);

// Sub feature flags
Setting('FEATURE_FLAG_STOCK_PRICE_TRACKING', true);
Setting('FEATURE_FLAG_STOCK_LOCATION_TRACKING', true);
Setting('FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING', true);
Setting('FEATURE_FLAG_STOCK_PRODUCT_OPENED_TRACKING', true);
Setting('FEATURE_FLAG_STOCK_PRODUCT_FREEZING', true);
Setting('FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD', true); // Activate the number pad in due date fields on (supported) mobile browsers
Setting('FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS', true);
Setting('FEATURE_FLAG_CHORES_ASSIGNMENTS', true);
Setting('FEATURE_FLAG_THERMAL_PRINTER', false);

// Feature settings
Setting('FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT', true); // When set to true, opened items will be counted as missing for calculating if a product is below its minimum stock amount
Setting('FEATURE_FLAG_AUTO_TORCH_ON_WITH_CAMERA', true); // Enables the torch automatically (if the device has one)
