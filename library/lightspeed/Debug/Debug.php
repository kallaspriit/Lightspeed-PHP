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
 * @subpackage Debug
 */

/**
 * Represents a route to a controller action
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Debug
 */
class Debug {

	/**
	 * Dumps the contents of a variable onto the screen or a file.
	 *
	 * If a filename is set, the contents are not sent to output but rather to
	 * the file specified in a readable format. If $alsoOutput is set true, the
	 * contents are also outputted even if written to file. By default, if
	 * outputting to file, the file is not truncated and data is added to the
	 * end of existing content. You can choose to truncate by setting
	 * $truncateFile true.
	 * 
	 * If the filename starts with a asterix "*", the filename is made relative
	 * to the LOG_PATH defined in paths.
	 *
	 * By default, the method only does anything when LS_DEBUG = true but you
	 * can override this by setting $ignoreDebugMode true.
	 *
	 * @param mixed $var Variable to process
	 * @param string $name Variable description
	 * @param string $filename Filename where to store the dump
	 * @param boolean $truncateFile Should the file be truncated first
	 * @param boolean $alsoOutput Should dump be outputed even when using file
	 * @param boolean $ignoreDebugMode Should method apply even if debug is off
	 */
	public static function dump($var, $name = 'Debug dump', $filename = null, $truncateFile = false, $alsoOutput = false, $ignoreDebugMode = false) {
		if (!empty($filename) && substr($filename, 0, 1) == '*' && defined('LOG_PATH')) {
			$filename = LOG_PATH.'/'.substr($filename, 1);
		}
		
		if (LS_DEBUG || $ignoreDebugMode) {
			if (!empty($filename)) {
				$data = '+'.str_repeat('-', 98).'+'.PHP_EOL;

				if (!empty($name)) {
					$data .= sprintf('| %-76s %s |', $name, date('Y-m-d H:i:s')).PHP_EOL;
					$data .= '+'.str_repeat('-', 98).'+'.PHP_EOL;
				}

				$backtrace = debug_backtrace();

				if (!empty($backtrace[0])) {
					$data .= sprintf(' %98s ', $backtrace[0]['file'].': '.$backtrace[0]['line']).PHP_EOL.PHP_EOL;
				}

				if (is_bool($var)) {
					$data .= $var === true ? 'TRUE' : 'FALSE';
				} else {
					$data .= (isset($var) ? print_r($var, true) : 'NULL');
				}
				
				$data .= PHP_EOL.PHP_EOL.PHP_EOL;
				
				file_put_contents($filename, $data, !$truncateFile ? FILE_APPEND : null);
			}

			if (empty($filename) || $alsoOutput) {
				echo '<br clear="all"/>';

				if (!empty($name)) {
					echo '<fieldset class="debug-dump"><legend>'.$name.'</legend>';
				}

				echo '<pre>';
				
				if (is_bool($var)) {
					echo $var === true ? '<em>TRUE</em>' : '<em>FALSE</em>';
				} else {
					echo (isset($var) ? htmlspecialchars(print_r($var, true)) : '<em>NULL</em>');
				}
				
				echo '</pre>';

				if (!empty($name)) {
					echo '</fieldset>';
				}
			}
		}
	}
}

?>