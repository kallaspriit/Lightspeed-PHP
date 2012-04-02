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

// Require the root class
require_once LIGHTSPEED_PATH.'/Router/RouteBase.php';

/**
 * Router matches a request to a application route.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Router
 */
class RouterBase {

	/**
	 * Parsed routes.
	 *
	 * This is lazy-loaded as required only once per request and from cache if
	 * possible.
	 *
	 * @var array
	 */
	protected $routes;

	/**
	 * Matches a request to a route defined in application/config/routes.php.
	 *
	 * If no route could be matched, an exception is thrown.
	 *
	 * By default cache is used to resolve the route so identical route paths
	 * will match to a route object without even parsing and matching them,
	 * fast local cache is used instead.
	 *
	 * @param HttpRequest $request Request to match
	 * @param boolean $useCache Should cache be used if possible
	 * @return Route Matched route object
	 * @throws Exception If no route could be matched
	 */
	public function matchRoute(
		HttpRequest $request,
		$useCache = LS_USE_SYSTEM_CACHE
	) {
		$cacheKey = 'lightspeed.router-route|'.$request->getRoutePath();
		$route = $useCache ? Cache::fetchLocal($cacheKey) : false;

		if ($route !== false) {
			return $route;
		}

		$routes = $this->getRoutes($useCache);

		foreach ($routes as $route) {
			if ($route->matches($request->getRoutePath())) {
				Cache::storeLocal($cacheKey, $route, LS_TTL_ROUTES);

				return $route;
			}
		}

		return null;
	}

	/**
	 * Returns all defined routes as {@see Link} objects.
	 *
	 * The routes are cached, see {@see LS_TTL_ROUTES} constant to change the
	 * time-to-live seconds.
	 *
	 * @param boolean $useCache Should cache be used to fetch the routes
	 * @return array
	 */
	public function getRoutes($useCache = LS_USE_SYSTEM_CACHE) {
		if ($this->routes !== null) {
			return $this->routes;
		}

		$cacheKey = 'lightspeed.routes';
		$this->routes = $useCache ? Cache::fetchLocal($cacheKey) : false;

		if ($this->routes !== false) {
			return $this->routes;
		}

		$_routes = null;

		// Route configuration contains definitions in $_routes variable
		require CONFIG_PATH.'/routes.php';

		$this->routes = $this->parseRoutes($_routes);

		Cache::storeLocal($cacheKey, $this->routes, LS_TTL_ROUTES);

		return $this->routes;
	}
	
	/**
	 * Returns a route by name.
	 * 
	 * Returns null if route does not exist.
	 * 
	 * @param string $name Name of the route
	 * @return RouteBase|null The route or null if not found 
	 */
	public function getRoute($name) {
		if (!isset($this->routes[$name])) {
			return null;
		}
		
		return $this->routes[$name];
	}
	
	/**
	 * Sets a route by name.
	 * 
	 * Overrites existing if a route with the same name exists.
	 * 
	 * @param string $name Name of the route
	 * @param RouteBase $route The route to add
	 */
	public function setRoute($name, RouteBase $route) {
		$this->routes[$name] = $route;
	}

	/**
	 * Parses route definitions into an array of route objects.
	 *
	 * The route definitions should be an array containing arrays containing:
	 * - path - the route path
	 * - controller - the controller name
	 * - action - the action name
	 *
	 * @param array $routeDefinitions Route definitions
	 * @return array
	 */
	public function parseRoutes(array $routeDefinitions = array()) {
		$routes = array();

		foreach ($routeDefinitions as $routeName => $routeDefinition) {
			$defaults = array();

			foreach ($routeDefinition as $paramKey => $paramValue) {
				if (
					!in_array($paramKey, array('path', 'controller', 'action'))
				) {
					$defaults[$paramKey] = $paramValue;
				}
			}

			$routes[$routeName] = $this->createRoute(
					$routeName,
					$routeDefinition['path'],
					$routeDefinition['controller'],
					$routeDefinition['action'],
					$defaults
			);
		}

		return $routes;
	}

	/**
	 * Compiles a url based on defined route and parameters.
	 *
	 * The parameters is optional and only needed for routes with variables. It
	 * is then expected to be an associative array with keys that match route
	 * variable names and values are replaced into the route instead of the
	 * appopriate variable definitions.
	 *
	 * If caching is enabled and $useCache true, if a route url with the same
	 * name and parameters has been requested before, the cached result is
	 * returned. Fast local cache is used for this.
	 *
	 * Parameters with defaults that were not given as route parameters are not
	 * added to the route. If a parameter without a default is left out, an
	 * exception is thrown.
	 *
	 * Route is generated in currently active language.
	 *
	 * @param string $routeName Name of the route to build on
	 * @param array $parameters Optional parameters to use as route variables
	 * @param boolean $useCache Should cache be used
	 * @param boolean $useTranslator Should translator be used if possible
	 * @throws Exception If route is not defined or passed invalid parameters
	 * @return string The compiled route url
	 * @throws Exception if route parameter or token translation does not exist
	 */
	public function makeUrl(
		$routeName,
		array $parameters = array(),
		$useCache = LS_USE_SYSTEM_CACHE,
		$useTranslator = true
	) {
		$cacheKey = 'lightspeed.route-url|'
			.$routeName.'.'
			.serialize($parameters);

		$url = $useCache ? Cache::fetchLocal($cacheKey) : false;

		// @codeCoverageIgnoreStart
		if ($url !== false) {
			return $url;
		}
		// @codeCoverageIgnoreEnd

		$routes = $this->getRoutes();

		if (!array_key_exists($routeName, $routes)) {
			throw new Exception('Route called "'.$routeName.'" is not defined');
		}

		$route = $routes[$routeName];
		$tokens = $route->getTokens();
		$defaults = $route->getDefaults();

		// Only use translations if Translator class exists
		// @codeCoverageIgnoreStart
		if (!class_exists('Translator', false)) {
			$useTranslator = false;
		}
		// @codeCoverageIgnoreEnd

		$url = '';
		
		$tokenCount = count($tokens);

		foreach ($tokens as $tokenKey => $token) {
			if (substr($token, 0, 1) == ':') {
				$bracketPos = strpos($token, '[');
				$parameterName = substr($token, 1);

				if ($bracketPos !== false) {
					$parameterName = substr($parameterName, 0, $bracketPos - 1);
				}

				if (isset($parameters[$parameterName])) {
					$url .= '/'.$parameters[$parameterName];
				} else {
					if (array_key_exists($parameterName, $defaults)) {
						if ($tokenKey < $tokenCount - 1) {
							$url .= '/'.$defaults[$parameterName];
						}
					} else {
						throw new Exception(
							'Missing route url parameter "'.$parameterName.'"'
						);
					}
				}
			} else if (substr($token, 0, 1) == '@') {
				$realToken = substr($token, 1);

				if ($useTranslator) {
					$translationKey = $realToken;
					$translatedToken = Translator::getInstance('routes')
						->translate($translationKey);

					$url .= '/'.$translatedToken;
				} else {
					$url .= '/'.$realToken;
				}
			} else {
				$url .= '/'.$token;
			}
		}
		
		Cache::storeLocal($cacheKey, $url, LS_TTL_ROUTES);

		return $url;
 	}

	/**
	 * Creates a new route.
	 *
	 * Override this method in your derived router to return an instance of your
	 * application-specific route implementation.
	 *
	 * @param string $name Name of the route
	 * @param string $path Route path definition
	 * @param string $controller Controller name
	 * @param string $action Action name
	 * @param array $defaults Default values of route variables
	 * @return RouteBase The created route
	 */
	protected function createRoute(
		$name,
		$path,
		$controller,
		$action,
		$defaults
	) {
		return new RouteBase($name, $path, $controller, $action, $defaults);
	}

	/**
	 * Adds a new route.
	 *
	 * Override this method in your derived router to return an instance of your
	 * application-specific route implementation.
	 *
	 * @param string $name Name of the route
	 * @param string $path Route path definition
	 * @param string $controller Controller name
	 * @param string $action Action name
	 * @param array $defaults Default values of route variables
	 * @return RouteBase The created route
	 */
	public function addRoute(
		$name,
		$path,
		$controller,
		$action,
		$defaults
	) {
		$this->routes[$name] = new RouteBase(
			$name,
			$path,
			$controller,
			$action,
			$defaults
		);
	}

}