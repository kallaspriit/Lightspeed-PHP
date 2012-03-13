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
 * @subpackage Autoload
 */

/**
 * Autoloads missing classes.
 *
 * @param string $className Name of the class that needs to be loaded
 */
function __autoload($className) {
	if (substr($className, -5) == 'Model') {
		require_once MODELS_PATH.'/'.$className.'.php';
	} else if (substr($className, -6) == 'Helper') {
		require_once HELPER_PATH.'/'.$className.'.php';
	} else if (substr($className, -7) == 'Service') {
		require_once SERVICES_PATH.'/'.$className.'.php';
	} else if ($className == 'Validator') {
		require_once APPLICATION_PATH.'/Validator.php';
	} else if ($className == 'Util') {
		require_once APPLICATION_PATH.'/Util.php';
	}
}