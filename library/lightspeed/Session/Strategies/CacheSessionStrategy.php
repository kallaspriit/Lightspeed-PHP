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
 * @subpackage Session
 */

// Require used classes
require_once LIGHTSPEED_PATH.'/Session/SessionStrategy.php';

/**
 * Enables storing data in persistent session across queries using any
 * cache handler.
 *
 * Notice that in a multiserver setup where any query is load-balanced
 * to hit a random web server, you should use a global cache method
 * such as memcached. In a single-server setup a faster APC implementation
 * can be useful.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Session
 */
class CacheSessionStrategy implements SessionStrategy {

	/**
	 * The apc handler to use.
	 *
	 * @var CacheStrategy
	 */
	protected $cache;

	/**
	 * The session identifier.
	 *
	 * @var string
	 */
	protected $sessionId;

	/**
	 * Cookie parameters to use to store session identifier.
	 *
	 * @var array
	 */
	protected $cookieParams = array(
		'lifetime' => 0,
		'path' => '/',
		'domain' => '',
		'secure' => false,
		'httponly' => false,
	);

	/**
	 * Name of the cookie to use to store session identifier.
	 *
	 * @var string
	 */
	protected $cookieName = 'LS_SESS_ID';

	/**
	 * Array of session values.
	 *
	 * @var array
	 */
	protected $values;

	/**
	 * Constructs the strategy.
	 *
	 * Needs any cache strategy as parameter for storing data.
	 *
	 * Notice that in a multiserver setup where any query is load-balanced
	 * to hit a random web server, you should use a global cache method
	 * such as memcached. In a single-server setup a faster APC implementation
	 * can be useful.
	 */
	public function __construct(CacheStrategy $cache) {
		$this->cache = $cache;
	}

	/**
	 * Stores the values in storage on destruction.
	 */
	public function  __destruct() {
		if ($this->cache !== null) {
			if ($this->values === null) {
				$this->values = array();
			}

			$ttl = LS_TTL_DEFAULT;

			if ($this->cookieParams['lifetime'] > 0) {
				// use the same as cookie if set to above zero
				$ttl = $this->cookieParams['lifetime'];
			}

			$this->cache->store(
				'lightspeed.session|'.$this->getId(),
				$this->values,
				$ttl
			);
		}
	}

	/**
	 * Sets cookie parameters to use to store session identifier
	 *
	 * This should be set before the first time the session is used.
	 *
	 * The parameters should contain following keys:
	 * - lifetime - lifetime of the cookie, default to 0 (browser close)
	 * - path - the path on the server in which the cookie will be available on
	 * - domain - the domain that the cookie is available to
	 * - secure - cookie should only be transmitted over a secure HTTPS only
	 * - httponly - cookie will be made accessible only through the HTTP protoc.
	 *
	 * @param array $parameters Parameters.
	 */
	public function setCookieParams(array $parameters) {
		$this->cookieParams = $parameters;
	}

	/**
	 * Returns current cookie parameters used to store session identifier.
	 *
	 * The parameters should contains following keys:
	 * - lifetime - lifetime of the cookie, default to 0 (browser close)
	 * - path - the path on the server in which the cookie will be available on
	 * - domain - the domain that the cookie is available to
	 * - secure - cookie should only be transmitted over a secure HTTPS only
	 * - httponly - cookie will be made accessible only through the HTTP protoc.
	 *
	 * @return array The cookie parameters
	 */
	public function getCookieParams() {
		return $this->cookieParams;
	}

	/**
	 * Sets new session identifier to use.
	 *
	 * @param string $sessionId Session id
	 */
	public function setId($sessionId) {
		$this->sessionId = $sessionId;

		setcookie(
			$this->cookieName,
			$this->sessionId,
			$this->cookieParams['lifetime'] > 0 ? time() + $this->cookieParams['lifetime'] : 0,
			$this->cookieParams['path'],
			$this->cookieParams['domain'],
			$this->cookieParams['secure'],
			$this->cookieParams['httponly']
		);
	}

	/**
	 * Returns current session id.
	 *
	 * If this is the first request and no session id exists, an identifier is
	 * generated and stored in a cookie.
	 *
	 * @return string The session identifier
	 */
	public function getId() {
		if ($this->sessionId === null) {
			if (!empty($_COOKIE[$this->cookieName])) {
				$this->sessionId = $_COOKIE[$this->cookieName];
			} else {
				$sessionId = md5(uniqid('', true));

				self::setId($sessionId);
			}
		}

		return $this->sessionId;
	}

	/**
	 * Sets session value.
	 *
	 * @param string $name Name of the property to set
	 * @param mixed $value Value of the property
	 * @return boolean Was setting the value successful
	 */
	public function set($name, $value) {
		$this->values[$name] = $value;
	}

	/**
	 * returns session value.
	 *
	 * Returns null if not set.
	 *
	 * @param string $name Name of the property to fetch
	 * @return mixed|null Session value or null if not set
	 */
	public function get($name) {
		if ($this->values === null) {
			$this->values = $this->loadValues();
		}
		
		if (array_key_exists($name, $this->values)) {
			return $this->values[$name];
		} else {
			return null;
		}
	}

	/**
	 * Loads current session values.
	 *
	 * @return array Array of data
	 */
	protected function loadValues() {
		return $this->cache->fetch(
			'lightspeed.session|'.$this->getId(),
			array()
		);
	}

}