<?php
// Constants file includes parameters, that while configurable in general, are
// the same on all of the machines and can thus be overwritten by source control

/**
 * Timezone to use for date-related operations
 */
define('LS_TIMEZONE', 'Europe/Tallinn');

/**
 * Encoding to use when dealing with multibyte string manipulation
 */
define('LS_ENCODING', 'UTF-8');

/**
 * Locale to use
 *
 * On a linux box, you may call "locale -a" to get a list of valid locales
 */
define('LS_LOCALE', 'en_US.utf8');

/**
 * How long to cache data by default
 */
define('LS_TTL_DEFAULT', 3600);

/**
 * How long to cache routes
 */
define('LS_TTL_ROUTES', LS_TTL_DEFAULT);

/**
 * How long to cache dispatch tokens resolved from routes
 */
define('LS_TTL_DISPATCH_RESOLVE', LS_TTL_DEFAULT);

/**
 * How long to cache translation of model class name to table name
 */
define('LS_TTL_MODEL_TABLE', LS_TTL_DEFAULT);

/**
 * How long to cache pagination navigation html
 */
define('LS_TTL_PAGINATION', LS_TTL_DEFAULT);

/**
 * Local cache strategy
 */
define('LS_CACHE_LOCAL', 'local');

/**
 * Global cache strategy
 */
define('LS_CACHE_GLOBAL', 'global');


/**
 * English language id
 */
define('LANGUAGE_ENGLISH', 1);


/**
 * Estonian language id
 *
 * Replace with anything you like or add new ones. Or you can remove them
 * all-together, make sure to update the Bootstrapper too.
 */
define('LANGUAGE_ESTONIAN', 2);

/**
 * The default language
 */
define('LANGUAGE_DEFAULT', LANGUAGE_ENGLISH);