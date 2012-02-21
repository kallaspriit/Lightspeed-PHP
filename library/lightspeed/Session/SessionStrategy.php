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

/**
 * Enables storing data in persistent session across queries.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Session
 */
interface SessionStrategy {

	/**
	 * Sets new session identifier to use.
	 *
	 * @param string $sessionId Session id
	 */
	public function setId($sessionId);

	/**
	 * Returns current session id.
	 *
	 * If this is the first request and no session id exists, an identifier is
	 * generated and stored in a cookie.
	 *
	 * @return string The session identifier
	 */
	public function getId();

	/**
	 * Sets session value.
	 *
	 * @param string $sessionId The session identifier
	 * @param string $name Name of the property to set
	 * @param mixed $value Value of the property
	 * @return boolean Was setting the value successful
	 */
	public function set($name, $value);

	/**
	 * Fetches session value.
	 *
	 * Returns null if not set.
	 *
	 * @param string $name Name of the property to fetch
	 * @return mixed|null Session value or null if not set
	 */
	public function get($name);
}