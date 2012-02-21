<?php
/**
 * Array of route definitions
 *
 * The route paths contain the tokens seperated by dashes.
 *
 * If a token starts with a colon, it is treated as a variable and it will be
 * replaced with user-provided value and the value will be available to the
 * action that gets called. If at the end of a variable token, there is a
 * character class in the brackets, the route is matched only if the url value
 * matches the class. The following classes are supported:
 * - [int] - the variable value must be an integer (either positive/negative)
 * - [+int] - the variable value must be a positive integer (1..n)
 *
 * If a token starts with @-character, the token is translated before being
 * matched. It also matches in untranslated form.
 *
 * If a token does not begin with "@" or ":", it is treated as a static name
 * and URL token at the same place must match it.
 */
$_routes = array(
	
	'index' => array(
		'path' => '/',
		'controller' => 'index',
		'action' => 'index'
	),
);