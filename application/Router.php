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
 * @package Application
 * @subpackage Router
 */

// Require the lightspeed base router implementation
require_once LIGHTSPEED_PATH.'/Router/RouterBase.php';

// Require application-specific route implementation
require_once APPLICATION_PATH.'/Route.php';

/**
 * Application router class.
 *
 * Matches the user-provided route path to a route.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Application
 * @subpackage Router
 */
class Router extends RouterBase {
	
	/**
	 * You can override any of the RouterBase methods if you wish the router
	 * to work differently then the default implementation
	 */
	
	/**
	 * Returns a route to a controller action that handles requesting a route
	 * that does not exist.
	 *
	 * @param HttpRequest $request The invalid request
	 * @return Route Route to page not found handler action
	 */
	public function getPageNotFoundRoute(HttpRequest $request) {
		$route = new RouteBase(
			'page-not-found', // not really important
			'/page-not-found', // not really important either
			'error',
			'page-not-found'
		);

		$route->setParams(array(
			'request' => $request
		));

		return $route;
	}
	
	/**
	 * Returns a route to a controller action that handles requesting a
	 * controller that does not exist.
	 *
	 * @param HttpRequest $request The invalid request
	 * @param DispatchToken $dispatchToken Generated dispatch token
	 * @return Route Route to controller not found handler action
	 */
	public function getInvalidControllerRoute(
		HttpRequest $request,
		DispatchToken $dispatchToken
	) {
		$route = new RouteBase(
			'invalid-controller', // not really important
			'/invalid-controller', // not really important either
			'error',
			'invalid-controller'
		);

		$route->setParams(array(
			'request' => $request,
			'dispatch-token' => $dispatchToken
		));

		return $route;
	}
	
	/**
	 * Returns a route to a controller action that handles requesting a
	 * controller action that does not exist.
	 *
	 * @param HttpRequest $request The invalid request
	 * @param DispatchToken $dispatchToken Generated dispatch token
	 * @return Route Route to controller action not found handler action
	 */
	public function getInvalidActionRoute(
		HttpRequest $request,
		DispatchToken $dispatchToken
	) {
		$route = new RouteBase(
			'invalid-action', // not really important
			'/invalid-action', // not really important either
			'error',
			'invalid-action'
		);

		$route->setParams(array(
			'request' => $request,
			'dispatch-token' => $dispatchToken
		));

		return $route;
	}
	
	/**
	 * Returns a route to a controller action that handles requesting a
	 * controller action that does not exist.
	 *
	 * @param HttpRequest $request The invalid request
	 * @return Route Route to controller action not found handler action
	 */
	public function getSpecialRoute(
		HttpRequest $request
	) {
		$requestedUrl = trim($request->getRoutePath(),'/');
		
		$requestedPages = explode('/', $requestedUrl);
		$requestedPage = $requestedPages[0];

		// check whether page exists, use service or something..
		if (!empty($requestedPage) && MenuService::checkIfMenuExistsByDynamicUrl($requestedPage)) {
			$route = new RouteBase(
				'page', // not really important
				'/page', // not really important either
				'page',
				'show'
			);

			$route->setParams(array(
				'request' => $request,
				'page' => $requestedUrl
			));
			
			return $route;
		}
		
		return null;
	}
	
	/**
	 * Returns a route to a controller action that handles displaying an
	 * internal application error.
	 *
	 * @param HttpRequest $request The invalid request
	 * @param DispatchToken $dispatchToken Generated dispatch token
	 * @param Exception $exception The exception that triggered this
	 * @return Route Route to controller action not found handler action
	 */
	public function getApplicationErrorRoute(
		HttpRequest $request,
		DispatchToken $dispatchToken,
		Exception $exception
	) {
		$route = new RouteBase(
			'application-error', // not really important
			'/application-error', // not really important either
			'error',
			'application-error'
		);

		$route->setParams(array(
			'request' => $request,
			'dispatch-token' => $dispatchToken,
			'exception' => $exception
		));

		return $route;
	}
	
	/**
	 * Creates a new route.
	 *
	 * Overrides to return an instance of application Route instead of the
	 * RouteBase.
	 *
	 * @param string $name Name of the route
	 * @param string $path Route path definition
	 * @param string $controller Controller name
	 * @param string $action Action name
	 * @param array $defaults Default values of route variables
	 * @return Route The created route
	 */
	protected function createRoute($name, $path, $controller, $action, $defaults) {
		return new Route($name, $path, $controller, $action, $defaults);
	}
}