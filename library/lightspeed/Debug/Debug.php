<?php
/**
 * Lightspeed high-performance hiphop-php optimized PHP framework
 *
 * Copyright (C) <2011> by <Priit Kallas>
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
					$data .= sprintf('| %-76s %s |', $name, date('d.m.Y H:i:s')).PHP_EOL;
					$data .= '+'.str_repeat('-', 98).'+'.PHP_EOL;
				}

				$backtrace = debug_backtrace();

				if (!empty($backtrace[0])) {
					$data .= sprintf(' %98s ', $backtrace[0]['file'].': '.$backtrace[0]['line']).PHP_EOL.PHP_EOL;
				}
				
				if ($var instanceof Exception) {
					$data .= self::formatException($var);
				} else if (is_bool($var)) {
					$data .= $var === true ? 'TRUE' : 'FALSE';
				} else if ($var === null) {
					$data .= 'NULL';
				} else if (empty($var)) {
					$data .= 'EMPTY';
				} else {
					$data .= print_r($var, true);
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
				
				if ($var instanceof Exception) {
					echo self::formatException($var);
				} else if (is_bool($var)) {
					echo $var === true ? '<em>TRUE</em>' : '<em>FALSE</em>';
				} else if ($var === null) {
					echo '<em>NULL</em>';
				} else if (empty($var)) {
					echo '<em>EMPTY</em>';
				} else {
					echo htmlspecialchars(print_r($var, true));
				}
				
				echo '</pre>';

				if (!empty($name)) {
					echo '</fieldset>';
				}
			}
		}
	}
	
	public static function formatException(Exception $e) {
		$out = get_class($e).' "'.$e->getMessage().'"'.($e->getCode() != 0 ? ' ['.$e->getCode().']' : '').PHP_EOL;
		
		$trace = $e->getTrace();
		
		if (!empty($trace)) {
			foreach ($trace as $key => $item) {
				$out .= '  #'.(count($trace) - $key).' '.(isset($item['file']) ? $item['file'].': '.$item['line'] : $e->getFile().': '.$e->getLine()).PHP_EOL;
				$out .= '    '.(!empty($item['class']) ? $item['class'].$item['type'] : '').$item['function'];
				
				if (!empty($item['args'])) {
					$out .= '('.PHP_EOL;
					
					$parameterNames = array();
					
					if (isset($item['class'])) {
						$class = new ReflectionClass($item['class']);
						$method = $class->getMethod($item['function']);
						$parameters = $method->getParameters();
						
						foreach ($parameters as $parameterInfo) {
							$parameterNames[] = $parameterInfo->getName();
						}
					}
					
					foreach ($item['args'] as $argKey => $arg) {
						$out .= '      '.(isset($parameterNames[$argKey]) ? $parameterNames[$argKey].' = ' : '').self::stringify($arg).($argKey < count($item['args']) - 1 ? ',' : '').PHP_EOL;
					}

					$out .= '    )'.PHP_EOL;
				} else {
					$out .= '()'.PHP_EOL;
				}
			}
		}
		
		$depth = 1;
		
		if (func_num_args() > 1) {
			$depth = func_get_arg(1);
		}
		
		$previous = $e->getPrevious();
		
		if (isset($previous)) {
			$out .= '> Previous:'.PHP_EOL;
			
			$previousOut = self::formatException($previous, $depth + 1);
			$lines = explode(PHP_EOL, $previousOut);
			$paddedPreviousOut = '';
			
			foreach ($lines as $line) {
				$paddedPreviousOut .= str_repeat(' ', $depth * 2).$line.PHP_EOL;
			}
			
			$out .= $paddedPreviousOut.PHP_EOL;
		}
		
		return $out;
	}
	
	public static function formatArray(array $array) {
		$out = '';
		
		foreach ($array as $key => $value) {
			if (!empty($out)) {
				$out .= ', ';
			}
			
			try {
				$out .= self::stringify($key).': '.self::stringify($value);
			} catch (Exception $e) {
				$out .= self::stringify($key) .': '.self::stringify($value);
			}
		}
		
		return '['.$out.']';
	}
	
	public static function stringify($var) {
		if (is_array($var)) {
			return self::formatArray($var);
		} else if (is_object($var)) {
			$members = get_object_vars($var);
			return 'instance of '.get_class($var).' '.self::formatArray($members);
		} else if (is_numeric($var)) {
			return $var;
		} else {
			return gettype($var) == 'string' || settype($var, 'string') ? strval($var) : print_r($var, true);
		}
	}
	
}

?>