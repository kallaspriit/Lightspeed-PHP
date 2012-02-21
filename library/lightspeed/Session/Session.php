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
 * Enables storing data in persistent session across queries.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Session
 */
class Session {

	/**
	 * The strategy used to store and retrieve session data.
	 * 
	 * @var SessionStrategy
	 */
	protected static $strategy;

	/**
	 * Sets session strategy to use.
	 *
	 * @param SessionStrategy $strategy Strategy to use
	 */
	public static function setStrategy(SessionStrategy $strategy) {
		self::$strategy = $strategy;
	}

	/**
	 * Returns current strategy.
	 *
	 * @return SessionStrategy
	 */
	public static function getStrategy() {
		return self::$strategy;
	}

	/**
	 * Sets new session identifier to use.
	 *
	 * @param string $sessionId Session id
	 */
	public static function setId($sessionId) {
		self::$strategy->setId($sessionId);
	}

	/**
	 * Returns current session id.
	 *
	 * If this is the first request and no session id exists, an identifier is
	 * generated and stored in a cookie.
	 *
	 * @return string The session identifier
	 */
	public static function getId() {
		return self::$strategy->getId();
	}

	/**
	 * Sets session value.
	 *
	 * @param string $name Name of the property to set
	 * @param mixed $value Value of the property
	 * @return boolean Was setting the value successful
	 */
	public static function set($name, $value) {
		return self::$strategy->set($name, $value);
	}

	/**
	 * Removes session value.
	 * 
	 * Actually just sets it's value to null.
	 *
	 * @param string $name Name of the property to set
	 * @return boolean Was setting the value successful
	 */
	public static function remove($name) {
		return self::$strategy->set($name, null);
	}

	/**
	 * Returns session value.
	 *
	 * Returns given default value if not set that defaults to null.
	 *
	 * @param string $name Name of the property to fetch
	 * @param mixed $default Default value to return
	 * @return boolean Was setting the value successful
	 */
	public static function get($name, $default = null) {
		$value = self::$strategy->get($name);

		if ($value === null) {
			return $default;
		}

		return $value;
	}
}