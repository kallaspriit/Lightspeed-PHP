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
 * @subpackage Request-Response
 */

/**
 * Represents a request that the application can use to decide which action to
 * take.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Request-Response
 */
class HttpRequest {

	/**
	 * The raw path representing a user-provided route
	 *
	 * @var string
	 */
	protected $routePath;
	
	/**
	 * Parameters parsed out of the route string.
	 *
	 * For example, route /action/show/id/1 is parsed into two parameters
	 * - action => show
	 * - id => 1
	 *
	 * If there is uneven number of tokens, the value of the last token becomes
	 * true, for example route /action/show/id/1/sorted is parsed into:
	 * - action => show
	 * - id => 1
	 * - sorted => true
	 *
	 * @var array
	 */
	protected $routeParameters;

	/**
	 * Combined request parameters, includes data from GET, POST and COOKIE
	 *
	 * @var array
	 */
	protected $requestParameters;

	/**
	 * Request parameters from GET
	 *
	 * @var array
	 */
	protected $urlParameters;

	/**
	 * Request parameters from POST
	 *
	 * @var array
	 */
	protected $postParameters;

	/**
	 * Request parameters from FILES
	 *
	 * @var array
	 */
	protected $filesParameters;

	/**
	 * Constructs the request with parameters.
	 *
	 * Dashes from the beginning and the end of the route path are trimmed.
	 *
	 * @param string $routePath The raw path representing a user-provided route
	 * @param array $requestParameters Combined request parameters
	 * @param array $urlParameters Request parameters from GET
	 * @param array $postParameters Request parameters from POST
	 * @param array $filesParameters Request parameters from FILES
	 */
	public function __construct(
		$routePath,
		array $requestParameters = array(),
		array $urlParameters = array(),
		array $postParameters = array(),
		array $filesParameters = array()
	) {
		$this->routePath = urldecode(trim($routePath, '/'));
		$this->requestParameters = $requestParameters;
		$this->urlParameters = $urlParameters;
		$this->postParameters = $postParameters;
		$this->filesParameters = $filesParameters;
	}

	/**
	 * Returns the route path as requested.
	 *
	 * Dashes from the beginning and the end of the route path are trimmed.
	 *
	 * @return string Route path
	 */
	public function getRoutePath() {
		return $this->routePath;
	}
	
	/**
	 * Returns all request parameters.
	 * 
	 * @return array 
	 */
	public function getParams() {
		return $this->requestParameters;
	}

	/**
	 * Returns a request parameter by name.
	 *
	 * If the parameter does not exist, the default value is returned that
	 * defaults to null.
	 *
	 * @param string $name Name of the parameter
	 * @param mixed $defaultValue Default value to return when not set
	 * @return mixed Parameter value if set, the default otherwise
	 */
	public function getParam($name, $defaultValue = null) {
		if (array_key_exists($name, $this->requestParameters)) {
			return $this->requestParameters[$name];
		}

		return $defaultValue;
	}

	/**
	 * Returns a parameter parsed from route.
	 *
	 * If the parameter does not exist, the default value is returned that
	 * defaults to null.
	 *
	 * @param string $name Name of the parameter
	 * @param mixed $defaultValue Default value to return when not set
	 * @return mixed Parameter value if set, the default otherwise
	 */
	public function getRouteParam($name, $defaultValue = null) {
		$routeParameters = $this->getRouteParams();

		if (array_key_exists($name, $routeParameters)) {
			return $routeParameters[$name];
		}

		return $defaultValue;
	}

	/**
	 * Returns a GET parameter by name.
	 *
	 * If the parameter does not exist, the default value is returned that
	 * defaults to null.
	 *
	 * @param string $name Name of the parameter
	 * @param mixed $defaultValue Default value to return when not set
	 * @return mixed Parameter value if set, the default otherwise
	 */
	public function getUrlParam($name, $defaultValue = null) {
		if (array_key_exists($name, $this->urlParameters)) {
			return $this->urlParameters[$name];
		}

		return $defaultValue;
	}

	/**
	 * Returns a POST parameter by name.
	 *
	 * If the parameter does not exist, the default value is returned that
	 * defaults to null.
	 *
	 * @param string $name Name of the parameter
	 * @param mixed $defaultValue Default value to return when not set
	 * @return mixed Parameter value if set, the default otherwise
	 */
	public function getPostParam($name, $defaultValue = null) {
		if (array_key_exists($name, $this->postParameters)) {
			return $this->postParameters[$name];
		}

		return $defaultValue;
	}

	/**
	 * Returns whether the request contains any GET data.
	 *
	 * @return boolean
	 */
	public function isGet() {
		return !empty($this->urlParameters);
	}

	/**
	 * Returns whether the request contains any POST data.
	 *
	 * @return boolean
	 */
	public function isPost() {
		return !empty($this->postParameters);
	}

	/**
	 * Returns all REQUEST parameters as they were given in the contructor.
	 *
	 * @return array
	 */
	public function getRequestParams() {
		return $this->requestParameters;
	}

	/**
	 * Returns all GET parameters as they were given in the contructor.
	 *
	 * @return array
	 */
	public function getUrlParams() {
		return $this->urlParameters;
	}

	/**
	 * Returns all parameters parsed from route.
	 *
	 * Note that route parameters are lazy-loaded meaning that if you don't use
	 * them, they are not parsed.
	 *
	 * @return array
	 */
	public function getRouteParams() {
		if ($this->routeParameters == null) {
			$this->routeParameters =
				self::parseRouteParameters($this->routePath);
		}

		return $this->routeParameters;
	}

	/**
	 * Returns all POST parameters as they were given in the contructor.
	 *
	 * @return array
	 */
	public function getPostParams() {
		return $this->postParameters;
	}

	/**
	 * Returns all FILES parameters as they were given in the contructor.
	 *
	 * @return array
	 */
	public function getFileParams() {
		return $this->filesParameters;
	}

	/**
	 * Sets a request parameter.
	 *
	 * @param string $name Name of the parameter
	 * @param mixed $value Value of the parameter
	 */
	public function setParam($name, $value) {
		$this->requestParameters[$name] = $value;
	}

	/**
	 * Sets a GET request parameter.
	 *
	 * @param string $name Name of the parameter
	 * @param mixed $value Value of the parameter
	 */
	public function setUrlParam($name, $value) {
		$this->urlParameters[$name] = $value;
	}

	/**
	 * Sets a POST request parameter.
	 *
	 * @param string $name Name of the parameter
	 * @param mixed $value Value of the parameter
	 */
	public function setPostParam($name, $value) {
		$this->postParameters[$name] = $value;
	}

	/**
	 * Sets FILES parameters.
	 *
	 * @param array $parameters File parameters
	 */
	public function setFileParams(array $parameters) {
		$this->filesParameters = $parameters;
	}

	/**
	 * Parses parameters out of the route string.
	 *
	 * For example, route /action/show/id/1 is parsed into two parameters
	 * - action => show
	 * - id => 1
	 *
	 * If there is uneven number of tokens, the value of the last token becomes
	 * true, for example route /action/show/id/1/sorted is parsed into:
	 * - action => show
	 * - id => 1
	 * - sorted => true
	 *
	 * @param <type> $routePath
	 */
	protected static function parseRouteParameters($routePath) {
		$tokens = explode('/', $routePath);
		$tokenCount = count($tokens);
		$parameters = array();

		for ($i = 0; $i < $tokenCount; $i += 2) {
			$name = $tokens[$i];

			if (!empty($name)) {
				$value = $i + 1 < $tokenCount ? $tokens[$i + 1] : true;

				$parameters[$name] = $value;
			}
		}

		return $parameters;
	}

}
