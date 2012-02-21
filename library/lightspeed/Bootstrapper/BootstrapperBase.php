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
 * @subpackage Bootstrapper
 */

// Require used classes
require_once LIGHTSPEED_PATH.'/Cache/Cache.php';

/**
 * Base bootstrapper class initiating the most basic resources.
 *
 * Extend this class with your own to load all the required resources for
 * your project.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Bootstrapper
 */
abstract class BootstrapperBase {

	/**
	 * Bootstraps the application
	 *
	 * @param HttpRequest $request
	 */
	public function bootstrap(HttpRequest $request) {
		$this->bootstrapLightspeed($request);
		$this->bootstrapApplication($request);
	}
    
    /**
     * Called when the request has been handled.
     * 
     * @param HttpResponse $response The response about to be sent
     */
    public function onRequestComplete(HttpResponse $response) {}

	/**
	 * Initiates base resources and settings that are always required
	 *
	 * @param HttpRequest $request The request
	 */
	protected function bootstrapLightspeed(HttpRequest $request) {
		// as profiled, this can actually be amazingly slow so better set it in
		// php.ini
		date_default_timezone_set(LS_TIMEZONE);
		mb_internal_encoding(LS_ENCODING);
		setlocale(LC_ALL, LS_LOCALE);
	}

	// @codeCoverageIgnoreStart

	/**
	 * Abstract method that needs to be overriden in your bootstrap to load
	 * the resources required by your application.
	 *
	 * @param HttpRequest $request The request
	 */
	abstract protected function bootstrapApplication(HttpRequest $request);

	// @codeCoverageIgnoreEnd
}