<?php

// Define the root path of the deployment
define('ROOT_PATH', '..');

// Define the public path, files from this can be reached by browser
define('PUBLIC_PATH', '.');

// Define where to find translations specific files
define('JS_TRANSLATIONS_PATH', PUBLIC_PATH.'/translations');

// Define where to find application specific files
define('LOG_PATH', ROOT_PATH.'/logs');

// Define where to find application specific files
define('APPLICATION_PATH', ROOT_PATH.'/application');

// Define where to find application specific files
define('CONFIG_PATH', APPLICATION_PATH.'/config');

// Define where to find application services
define('SERVICES_PATH', APPLICATION_PATH.'/services');

// Define where to find application models
define('MODELS_PATH', APPLICATION_PATH.'/models');

// Define where to find application translations
define('TRANSLATIONS_PATH', APPLICATION_PATH.'/translations');

// Define where to find application controllers
define('CONTROLLERS_PATH', APPLICATION_PATH.'/controllers');

// Define where to find libraries
define('LIBRARY_PATH', ROOT_PATH.'/library');

// Define where to find libraries
define('LIGHTSPEED_PATH', LIBRARY_PATH.'/lightspeed');

// Define where to find view helpers
define('HELPER_PATH', APPLICATION_PATH.'/helpers');

// Define where to find view layouts
define('LAYOUT_PATH', APPLICATION_PATH.'/layouts');

// Define where to find views
define('VIEW_PATH', APPLICATION_PATH.'/views');

// Define where to find partials
define('PARTIAL_PATH', APPLICATION_PATH.'/partials');

// Define where to store the list of executed database updates
define('DB_UPDATES_PATH', CONFIG_PATH.'/db-updates.txt');

// Where to store temporary files that may be deleted at any time
define('TEMP_PATH', ROOT_PATH.'/temp');