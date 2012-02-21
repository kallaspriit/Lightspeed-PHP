<?php
/**
 * Lightspeed high-performance hiphop-php optimized PHP framework
 *
 * Copyright (C) <2012> by <Priit Kallas>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Router
 */

/**
 * Represents a route to a controller action.
 *
 * Includes route matching.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Router
 */
class RouteBase {

	/**
	 * The name of the route
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The route path definition
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Controller name that should handle the request
	 *
	 * @var string
	 */
	protected $controller;

	/**
	 * Controller action name that should handle the request
	 *
	 * @var string
	 */
	protected $action;

	/**
	 * Default values of parameters
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Route tokens.
	 *
	 * All the tokens in route path that are seperated with dashes.
	 *
	 * This is a member so it would be generated only once and then the route
	 * can be cached.
	 *
	 * @var array
	 */
	protected $tokens;

	/**
	 * Parameter values.
	 *
	 * This field is only set after the route has matched a route path and
	 * can be queried with {@see Route::getParams()} and
	 * {@see Route::getParam()}.
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 * Contructs the route, setting the information.
	 *
	 * @param string $name Name of the route
	 * @param string $path Route path definition
	 * @param string $controller Controller name
	 * @param string $action Action name
	 * @param array $defaults Default values of route variables
	 */
	public function __construct(
		$name,
		$path,
		$controller,
		$action,
		array $defaults = array()
	) {
		$this->name = $name;
		$this->path = $path;
		$this->controller = $controller;
		$this->action = $action;
		$this->defaults = $defaults;
		$this->parameters = $this->defaults;
		$this->tokens = self::parseTokens($path);
	}

	/**
	 * Returns a unique hash for given route.
	 *
	 * This consists of the route name and parameters.
	 *
	 * @return string
	 */
	public function  __toString() {
		$serializedParameters = null;
		
		try {
			$serializedParameters = serialize($this->parameters);
		} catch (Exception $e) {
			$serializedParameters = 'unserializable';
		}
		
		return $this->name.'|'.$serializedParameters;
	}

	/**
	 * Checks whether given route path matches this route.
	 *
	 * The route is matched to route path one token at a time using rules:
	 * 1. If the token is a variable starting with ":", any value must exist
	 *    on the same place in route path. If the variable contains a class in
	 *    brackets (for example ":id[+int]"), it is verified that the token
	 *    value in route path is of correct type.
	 * 2. If the token is a translatable string starting with "@", token coming
	 *    from route path must have either the untranslated or translated value
	 *    of given token. For example if route "'/@view-topic/:id" has token
	 *    "view-topic" translated to "show", both route paths "/view-topic/1"
	 *    in untranslated form and "/show/1" would match the route.
	 * 3. If the token is plain static string, the same token must exist in
	 *    the matched route path.
	 *
	 * @param string $routePath Route path to match
	 * @param boolean $useTranslator Should translator be used if possible
	 * @return boolean Does given route path match current route
	 */
	public function matches($routePath, $useTranslator = true) {
		// first, check for exact match, maybe we get lucky
		if ($routePath == $this->path) {
			return true;
		}

		$matchTokens = self::parseTokens($routePath);
		$matchTokenCount = count($matchTokens);
		$routeTokenCount = count($this->tokens);

		if ($matchTokenCount > $routeTokenCount) {
			return false; // match path contains more tokens then route
		}

		// @codeCoverageIgnoreStart
		if (!class_exists('Translator', false)) {
			$useTranslator = false;
		}
		// @codeCoverageIgnoreEnd

		for ($i = 0; $i < $routeTokenCount; $i++) {
			$routeToken = $this->tokens[$i];
			$matchToken = isset($matchTokens[$i]) ? $matchTokens[$i] : null;

			$typeConstraint = null;

			if ($routeToken !== null) {
				$bracketPos = strpos($routeToken, '[');
				if ($bracketPos !== false) {
					$typeConstraint = substr($routeToken, $bracketPos);
					$routeToken = substr($routeToken, 0, $bracketPos);
				}
			}

			if ($matchToken !== null) {
				if (substr($routeToken, 0, 1) == ':') {
					if (!$this->isValidTokenType($typeConstraint, $matchToken)) {
						return false;
					}

					$parameterName = substr($routeToken, 1);

					$this->parameters[$parameterName] = $matchToken;
				} else if (substr($routeToken, 0, 1) == '@') {
					$realRouteToken = substr($routeToken, 1);

					if ($useTranslator) {
						$translationKey = $realRouteToken;
						$translatedToken = Translator::getInstance('routes')
							->translate($translationKey);

						if (
							$translatedToken != $matchToken
							&& $realRouteToken != $matchToken
						) {
							return false;
						}
					} else if ($realRouteToken != $matchToken) {
						return false;
					}
				} else if ($routeToken != $matchToken) {
					return false;
				}
			} else if (!array_key_exists(substr($routeToken, 1), $this->defaults)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns route name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns route path.
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Sets route path.
	 *
	 * $param string $path Path to use
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * Returns route defined controller name.
	 *
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * Returns route defined controller action name.
	 *
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Returns route parameter defaults values.
	 *
	 * @return string
	 */
	public function getDefaults() {
		return $this->defaults;
	}

	/**
	 * Returns the values of route parameters.
	 *
	 * The data is only valid after the route has matched a route path from
	 * which the values were extracted.
	 *
	 * @return array Route parameters
	 */
	public function getParams() {
		return $this->parameters;
	}

	/**
	 * Sets route parameters.
	 *
	 * You should normally not need to use it, it's used internally.
	 *
	 * @param array $parameters Route parameters to use
	 */
	public function setParams(array $parameters) {
		$this->parameters = $parameters;
	}
	
	/**
	 * Sets a single route parameter.
	 *
	 * @param string $name Name of the parameters
	 * @param mixed $value The value
	 */
	public function setParam($name, $value) {
		$this->parameters[$name] = $value;
	}

	/**
	 * Returns the values of a route parameter.
	 *
	 * The data is only valid after the route has matched a route path from
	 * which the values were extracted.
	 *
	 * If the requested route parameter has no value, the value defined in
	 * $default is returned.
	 *
	 * @return mixed Parameter value
	 */
	public function getParam($name, $default = null) {
		if (isset($this->parameters[$name])) {
			return $this->parameters[$name];
		}

		return $default;
	}

	/**
	 * Returns route tokens.
	 *
	 * Tokens are parts of the route path seperated by dashes.
	 *
	 * @return array Route tokens
	 */
	public function getTokens($useCache = LS_USE_SYSTEM_CACHE) {
		if (!$useCache) {
			$this->tokens = RouteBase::parseTokens($this->path);
		}
		
		return $this->tokens;
	}

	/**
	 * Parses route path into tokens seperated by dashes.
	 *
	 * @param string $path Path to tokenize
	 * @return array Parsed tokens
	 */
	public static function parseTokens($path) {
		return explode('/', trim($path, '/'));
	}

	/**
	 * Returns whether a match token is of corrent type by route token.
	 *
	 * @param string $typeConstraint The type constraint to check
	 * @param string $matchToken Match token to check type of
	 */
	protected function isValidTokenType($typeConstraint, $matchToken) {
		// [+int] requires match token to be ant int in range 1..n
		if (
			$typeConstraint == '[+int]'
			&& (
				(string)(int)$matchToken != $matchToken || (int)$matchToken < 1
			)

		) {
			return false;
		}
		
		// [page] requires match token to be ant int in range 1..n or the
		// translation for pager.label.all
		if (
			$typeConstraint == '[page]'
			&& (
				((string)(int)$matchToken != $matchToken
				|| (int)$matchToken < 1)
				&& $matchToken != Translator::get('pager.label.all')
			)

		) {
			return false;
		}

		// [int] requires match token to be an integer, negative also permitted
		// Got to be careful here, without the additional (string) cast, values
		// like "5" and "5x" are treated as same!
		if (
			$typeConstraint == '[int]'
			&& (string)(int)$matchToken != $matchToken
		) {
			return false;
		}

		return true;
	}

}