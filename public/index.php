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
 * @subpackage Bootstrap
 */

try {
	//xdebug_start_code_coverage(XDEBUG_CC_UNUSED);

	// Get the start time
	$startTime = microtime(true);

	// Show all errors until decided otherwise by the application
	error_reporting(-1);
	
	// Limit maximum execution time
	set_time_limit(10);

	// If default paths are changed, this relative path may beed to be changed too
	require_once '../application/config/paths.php';

	// Include the needed components to bootstrap
	require_once CONFIG_PATH.'/config.php';
	require_once CONFIG_PATH.'/constants.php';
	require_once LIGHTSPEED_PATH.'/Http/HttpRequest.php';
	require_once APPLICATION_PATH.'/Bootstrapper.php';
	require_once APPLICATION_PATH.'/Router.php';
	require_once APPLICATION_PATH.'/Dispatcher.php';
	require_once APPLICATION_PATH.'/FrontController.php';

	// Find route path by removing query string from request URI
	$requestUri = $_SERVER['REQUEST_URI'];
	$queryString = $_SERVER['QUERY_STRING'];
	$routePath = str_replace('?'.$queryString, '', $requestUri);

	// Instanciate the classes
	$request = new HttpRequest($routePath, $_REQUEST, $_GET, $_POST, $_FILES);
	$bootstrapper = new Bootstrapper();
	$frontController = new FrontController();
	$dispatcher = new Dispatcher();
	$router = new Router();

	// Bootstrap the application
	$bootstrapper->bootstrap($request);

	// Match the request to a route
	$route = $router->matchRoute($request);

	// If no route could be found, try to solve route path to action directly
	if ($route === null) {
		$route = $frontController->getDirectRoute(
			$request,
			$dispatcher
		);
	}
	
	// Didnt match, try special routes
	if ($route === null) {
		$route = $router->getSpecialRoute($request);
	}

	// If no route could still be found, get a not-found-route
	if ($route === null) {
		$route = $router->getPageNotFoundRoute($request);
	}

	// Resolve the route to a dispatch-token
	$dispatchToken = $dispatcher->resolve($route);

	$dispatchingLoop = true;
	
	do {
		try {
			// dispatch the request through front controller
			$response = $frontController->dispatch(
				$request,
				$bootstrapper,
				$router,
				$dispatcher,
				$route,
				$dispatchToken
			);
			
			$dispatchingLoop = false;
		} catch (InvalidControllerException $e) {
			// requested controller does not exist
			$route = $router->getInvalidControllerRoute(
				$request,
				$dispatchToken
			);

			$route->setParam('exception', $e);

			$newDispatchToken = $dispatcher->resolve($route);

			if ($newDispatchToken->isSameAs($dispatchToken)) {
				throw new Exception(
					'Dispatch loop created by being unable '.
					'to create controller "'.
					$dispatchToken->getControllerName().
					'" instance',
					0,
					$e
				);
			}

			$dispatchToken = $newDispatchToken;
		} catch (InvalidControllerActionException $e) {
			// controller action is not callable
			$route = $router->getInvalidActionRoute(
				$request,
				$dispatchToken
			);

			$newDispatchToken = $dispatcher->resolve($route);

			if ($newDispatchToken->isSameAs($dispatchToken)) {
				throw new Exception(
					'Dispatch loop created by being unable '.
					'to call controller action"'.
					$dispatchToken->getControllerName().
					'::'.$dispatchToken->getActionMethodName().'()',
					1,
					$e
				);
			}

			$dispatchToken = $newDispatchToken;
		} catch (Exception $e) {
			// controller action is not callable
			$route = $router->getApplicationErrorRoute(
				$request,
				$dispatchToken,
				$e
			);

			$newDispatchToken = $dispatcher->resolve($route);

			if ($newDispatchToken->isSameAs($dispatchToken)) {
				throw new Exception(
					'Dispatch loop created by recurring '.
					'application error',
					2,
					$e
				);
			}

			$dispatchToken = $newDispatchToken;
		}
	} while ($dispatchingLoop);
	
    // Notify bootstrapper that the request has been handled
    $bootstrapper->onRequestComplete($response);

	// Send the generated contents to the browser
	$response->send();

	if (LS_DEBUG) {
		// Display the time taken
		echo '<div style="position: fixed; right: 5px; bottom: 5px; color: #333333; font-size: 12px; font-family: Courier; z-index: 1000; background: rgba(255,255,255,0.5); padding: 0 3px; border-radius: 3px;">'.round(microtime(true) - $startTime, 6).'</div>';

		/*
		// show some debug info
		Debug::dump($routePath, 'Route path');
		Debug::dump($request, 'Request');
		Debug::dump($bootstrapper, 'Bootstrapper');
		Debug::dump($route, 'Route');
		Debug::dump($dispatchToken, 'DispatchToken');
		Debug::dump($frontController, 'FrontController');
		*/

		/*
		$coverage = xdebug_get_code_coverage();

		$linesExecuted = 0;
		$linesNotExecuted = 0;
		$linesTotal = 0;
		$fileCount = 0;

		foreach ($coverage as $file => $lines) {
			$fileLinesExecuted = 0;
			$fileLinesNotExecuted = 0;

			foreach ($lines as $lineNr => $timesExecuted) {
				if ($timesExecuted >= 1) {
					$fileLinesExecuted++;
				} else {
					$fileLinesNotExecuted++;
				}
			}

			$fileLinesTotal = $fileLinesExecuted + $fileLinesNotExecuted;
			$linesExecuted += $fileLinesExecuted;
			$linesNotExecuted += $fileLinesNotExecuted;
			$linesTotal += $fileLinesTotal;
			$fileCount++;

			echo '<strong>'.$file.':</strong> '.$fileLinesExecuted.'/'.$fileLinesTotal.' ('.round(($fileLinesExecuted * 100) / $fileLinesTotal).'%)<br/>'."\n";
		}

		echo '<strong>TOTAL:</strong> '.$linesExecuted.'/'.$linesTotal.' ('.round(($linesExecuted * 100) / $linesTotal).'%) in '.$fileCount.' files<br/>'."\n";
		*/
	}
	
	exit(0);
} catch (Exception $e) {
	if (defined('LS_DEBUG') && LS_DEBUG === true) {
		if (class_exists('Debug', false)) {
			Debug::dump($e, 'Exception occured');
		} else {
			echo '<pre>';
			var_dump($e);
			echo '</pre>';
		}
		
		exit(1);
	} else {
		echo '<pre>';
		var_dump($e);
		echo '</pre>';
		
		exit(2);
	}
}