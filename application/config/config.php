<?php
// Config file includes parameters that may be different on various machines of
// the application deployment and should not be overwritten by source updates.

/**
 * Is lightspeed debug mode enabled
 */
define('LS_DEBUG', true);

/**
 * Should cache be used.
 */
define('LS_USE_CACHE', true);

/**
 * Should local cache be used.
 */
define('LS_USE_LOCAL_CACHE', LS_USE_CACHE);

/**
 * Should local cache be used.
 */
define('LS_USE_GLOBAL_CACHE', LS_USE_CACHE);

/**
 * Should the lightspeed library use cache internally to improve internal
 * performance.
 *
 * You probably want this to be off during development as many changes to code
 * dont take immidiate effect.
 */
define('LS_USE_SYSTEM_CACHE', !LS_DEBUG);