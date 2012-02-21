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
 * @subpackage Dispatcher
 */

// Require the dispatch token implementation
require_once LIGHTSPEED_PATH.'/Dispatcher/DispatchToken.php';
require_once LIGHTSPEED_PATH.'/Router/RouteBase.php';

/**
 * Dispatcher takes a route as input and finds which controller action should
 * be called.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Dispatcher
 */
class DispatcherBase {

	/**
	 * Resolves a route to a dispatch token representing the actual controller
	 * action to call.
	 *
	 * @param RouteBase $route Route to resolve into dispatch token
	 * @param boolean $useCache Should cache be used
	 * @return DispatchToken
	 */
	public function resolve(
		RouteBase $route,
		$useCache = LS_USE_SYSTEM_CACHE
	) {
		$cacheKey = 'lightspeed.dispatch-token|'.$route->__toString();
		
		/*
		$dispatchToken = $useCache ? Cache::fetchLocal($cacheKey) : false;

		if ($dispatchToken !== false) {
			return $dispatchToken;
		}
		*/
		
		$controllerClassName = $this->translateControllerClassName(
			$route->getController()
		);
		
		$actionMethodName = $this->translateActionMethodName(
			$route->getAction()
		);

		$controllerClassFilename = $this->getControllerClassFilename(
			$controllerClassName
		);

		$dispatchToken =  new DispatchToken(
			$controllerClassName,
			$actionMethodName,
			$route->getParams(),
			$controllerClassFilename
		);

		//Cache::storeLocal($cacheKey, $dispatchToken, LS_TTL_DISPATCH_RESOLVE);

		return $dispatchToken;
	}

	/**
	 * Builds a dispatch token by controller and action names, parameters.
	 *
	 * Routes format means that controller and action names should be in format
	 * using lowercase characters and dashes, for example "user-manager" that is
	 * translated to controller name "UserManagerController" etc.
	 *
	 * @param string $controller Controller name in routes format
	 * @param string $action Action name in routes format
	 * @param array $parameters Request parameters
	 * @return DispatchToken Dispatch token to requested controller action
	 */
	public function build($controller, $action, array $parameters = array()) {
		$controllerClassName = $this->translateControllerClassName(
			$controller
		);
		$actionMethodName = $this->translateActionMethodName(
			$action
		);
		$controllerClassFilename = $this->getControllerClassFilename(
			$controllerClassName
		);

		return new DispatchToken(
			$controllerClassName,
			$actionMethodName,
			$parameters,
			$controllerClassFilename
		);
	}

	/**
	 * Formats a controller name to actual controller class name.
	 *
	 * For example, controller name "user-manager" is translated to
	 * "UserManagerController".
	 *
	 * @param string $controllerName Controller name to translate
	 * @return string Controller class name
	 * @throws Exception If invalid controller name is passed in
	 */
	public function translateControllerClassName($controllerName) {
		if (strpos($controllerName, '.') !== false) {
			throw new Exception(
				'Invalid controller name "'.$controllerName.'"'
			);
		}

		while (($dashPos = strpos($controllerName, '-')) !== false) {
			$controllerName = substr($controllerName, 0, $dashPos)
				.strtoupper(substr($controllerName, $dashPos + 1, 1))
				.substr($controllerName, $dashPos + 2);
		}

		return ucfirst($controllerName).'Controller';
	}

	/**
	 * Formats a action name to actual controller action method name.
	 *
	 * For example, action name "save-user" is translated to "saveUserAction".
	 *
	 * @param string $actionName Controller name to translate
	 * @return string Controller class name
	 * @throws Exception If invalid action name is passed in
	 */
	public function translateActionMethodName($actionName) {
		if (strpos($actionName, '.') !== false) {
			throw new Exception(
				'Invalid action name "'.$actionName.'"'
			);
		}

		while (($dashPos = strpos($actionName, '-')) !== false) {
			$actionName = substr($actionName, 0, $dashPos)
				.strtoupper(substr($actionName, $dashPos + 1, 1))
				.substr($actionName, $dashPos + 2);
		}

		return $actionName.'Action';
	}

	/**
	 * Returns filename where given controller class can be found in.
	 *
	 * @param string $controllerClassName Class name of the controller
	 * @return string Filename that should be included to use the class
	 */
	public function getControllerClassFilename($controllerClassName) {
		return CONTROLLERS_PATH.'/'.$controllerClassName.'.php';
	}
}