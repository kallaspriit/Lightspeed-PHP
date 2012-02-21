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

/**
 * Dispatch token represents a certain controller and action to call with
 * specific parameters.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Dispatcher
 */
class DispatchToken {

	/**
	 * Class name of the controller.
	 * 
	 * @var string
	 */
	protected $controllerClassName;

	/**
	 * Method name of the controller action to call.
	 *
	 * @var string
	 */
	protected $actionMethodName;

	/**
	 * Parameters extracted from route
	 *
	 * @var string
	 */
	protected $parameters = array();

	/**
	 * File that should be included to use the controller
	 *
	 * @var string
	 */
	protected $classFilename;

	/**
	 * Constructs the dispatch token.
	 *
	 * @param string $controllerClassName Controller class name
	 * @param string $actionMethodName Method name of the action to call
	 * @param array $parameters Parameters to pass to controller action
	 * @param string $classFilename Optional file that needs to be included
	 */
	public function  __construct(
		$controllerClassName,
		$actionMethodName,
		array $parameters = array(),
		$classFilename = null
	) {
		$this->controllerClassName = $controllerClassName;
		$this->actionMethodName = $actionMethodName;
		$this->parameters = $parameters;
		$this->classFilename = $classFilename;
	}

	/**
	 * Returns controller name in routes format.
	 *
	 * So ActiveUsersController becomes "active-users".
	 *
	 * @return string
	 */
	public function getControllerName() {
		return strtolower($this->controllerClassName[0]).
			$this->camelCaseToLowerDashes(
				substr($this->controllerClassName, 1, -10)
			);
	}

	/**
	 * Returns controller class name
	 *
	 * @return string
	 */
	public function getControllerClassName() {
		return $this->controllerClassName;
	}

	/**
	 * Returns action name in routes format.
	 *
	 * So usersListAction becomes "users-list".
	 *
	 * @return string
	 */
	public function getActionName() {
		return $this->camelCaseToLowerDashes(
			substr($this->actionMethodName, 0, -6)
		);
	}

	/**
	 * Returns method name of the controller action
	 *
	 * @return string
	 */
	public function getActionMethodName() {
		return $this->actionMethodName;
	}

	/**
	 * Returns file name that should be included to use the controller.
	 *
	 * @return string
	 */
	public function getControllerClassFilename() {
		return $this->classFilename;
	}

	/**
	 * Returns parameter by name.
	 *
	 * If no parameter has been set with given name, the given default value
	 * that defaults to null is returned.
	 *
	 * @param string $name Name of the parameter to request
	 * @param mixed $default value to return when parameter is not set
	 * @return mixed Parameter value
	 */
	public function getParam($name, $default = null) {
		if (array_key_exists($name, $this->parameters)) {
			return $this->parameters[$name];
		}

		return $default;
	}

	/**
	 * Returns route parameters
	 *
	 * @return array
	 */
	public function getParams() {
		return $this->parameters;
	}
	
	/**
	 * Returns whether given dispatch token is the same as another one if
	 * considering only controller class and action name.
	 * 
	 * @param DispatchToken $otherToken Token to compare to
	 * @return boolean Is the controller action same for both tokens
	 */
	public function isSameAs(DispatchToken $otherToken) {
		if (
			$otherToken->getControllerClassName()
				== $this->getControllerClassName()
			&& $otherToken->getActionMethodName()
				== $this->getActionMethodName()
		) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Converts CamelCaseString to lower-dash-seperated format.
	 *
	 * @param string $original The string to convert
	 * @return string Converted format string
	 */
	protected function camelCaseToLowerDashes($original) {
		$lowercase = strtolower($original);
		$formatted = '';

		for ($i = 0; $i < strlen($lowercase); $i++) {
			if ($lowercase[$i] != $original[$i]) {
				$formatted .= '-';
			}

			$formatted .= $lowercase[$i];
		}

		return $formatted;
	}
}