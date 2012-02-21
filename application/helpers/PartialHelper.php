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
 * @subpackage Helpers
 */

// Require used classes
require_once APPLICATION_PATH.'/View.php';

/**
 * Renders partial scripts.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Helpers
 */
class PartialHelper {

	/**
	 * Renders a partial script.
	 *
	 * The $script can be either a full filename (either relative or absolute)
	 * to a view script or just the name of the script without path and
	 * extension. In first case, the given filename is used to render the
	 * partial, in second case, the filename is assembled by prepending the
	 * {@see PARTIAL_PATH} constant and appending .php so for example just
	 * "footer" becomes "PARTIAL_PATH/footer.php".
	 *
	 * The data is used to provide the partial script with data to use. You may
	 * leave it null not to use any, set it to an array of data or provide an
	 * existing view object (such as $this in other views) to use the data in
	 * that.
	 *
	 * @param string $script Name or filename of the script
	 * @param View|array|null $data Data to use to render partial
	 * @return string Rendered partial contents
	 */
	public static function render($script, $data = null) {
		$script = self::resolveScript($script);

		if ($data instanceof View) {
			return $data->render($script);
		}

		$view = new View();

		if (is_array($data)) {
			$view->setData($data);
		}

		return $view->render($script);
	}
	
	/**
	 * Renders a partial script using caching.
	 *
	 * The $script can be either a full filename (either relative or absolute)
	 * to a view script or just the name of the script without path and
	 * extension. In first case, the given filename is used to render the
	 * partial, in second case, the filename is assembled by prepending the
	 * {@see PARTIAL_PATH} constant and appending .php so for example just
	 * "footer" becomes "PARTIAL_PATH/footer.php".
	 *
	 * The data is used to provide the partial script with data to use. You may
	 * leave it null not to use any, set it to an array of data or provide an
	 * existing view object (such as $this in other views) to use the data in
	 * that.
	 *
	 * Partials are cached in local cache by default but you may override this
	 * with $storage parameter by setting it to LS_CACHE_GLOBAL for global cache
	 * strategy.
	 *
	 * @param string $script Name or filename of the script
	 * @param View|array|null $data Data to use to render partial
	 * @param string|array|null $context The cache context identifier
	 * @param integer $timeToLive Number of seconds to keep the cache
	 * @param string $cacheStrategy One of LS_CACHE_.. strategy constants
	 * @return string Rendered partial contents
	 * @throws Exception If using invalid cache strategy
	 */
	public static function renderCached(
		$script,
		$data = null,
		$context = null,
		$timeToLive = LS_TTL_DEFAULT,
		$cacheStrategy = LS_CACHE_LOCAL
	) {
		$script = self::resolveScript($script);
		
		if (is_array($context)) {
			$context = serialize($context);
		}

		$cacheKey = 'lightspeed.partial-helper-content|'.$script.'.'.$context;
		$output = Cache::fetch($cacheStrategy, $cacheKey);

		if ($output === false) {
			$output = self::render($script, $data);

			Cache::store($cacheStrategy, $cacheKey, $output);
		}

		return $output;
	}

	/**
	 * Clears cache entry for given scrit in given context.
	 *
	 * Make sure to set the same cache strategy as used for storing.
	 *
	 * @param string $script Name or filename of the script
	 * @param string|array|null $context The cache context identifier
	 * @param string $cacheStrategy One of LS_CACHE_.. strategy constants
	 * @throws Exception If using invalid cache strategy
	 */
	public static function removeCache(
		$script,
		$context,
		$cacheStrategy = LS_CACHE_LOCAL
	) {
		$script = self::resolveScript($script);
		$cacheKey = 'lightspeed.partial-helper-content|'.$script.'.'.$context;

		Cache::remove($cacheStrategy, $cacheKey);
	}

	protected static function resolveScript($script) {
		if (strpos($script, '.') === false) {
			$script = PARTIAL_PATH.'/'.$script.'.php';
		}

		return $script;
	}

}