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
 * @subpackage Cache
 */

/**
 * Dummy cache strategy not really using cache at all.
 */
class DummyCacheStrategy extends CacheStrategy {
    
	/**
	 * Stores a value in cache.
	 *
	 * The tags may be null when not using tags, a single tag as a string or an
	 * array of tags. Tags are useful for clearing the cache of several entries
	 * at a time. For example you may have a large dataset that is pagiated and
	 * the contents of each page is cached seperately. Now you want to clear the
	 * cache of this dataset but don't easlily know many pages to clear. Just
	 * add a single tag to all the pages and clear by tag.
	 *
	 * @param string $key Key of the value to store
	 * @param mixed $value Value to store
	 * @param integer $timeToLiveSeconds How long to store the cache in seconds
	 * @param string|array|null $tags Entry tags
	 * @return boolean Was storing the value successful
	 */
	public function store($key, $value, $timeToLiveSeconds, $tags = null) {
		return true;
	}

	/**
	 * Returns a value from cache.
	 *
	 * If no cache match is found, the value defined in $default is returned.
	 *
	 * If an array of cache keys is given, an array containing the results is
	 * returned with the requested keys as array keys. If some key is not found,
	 * it is not returned back.
	 *
	 * @param string|array $key Cached data key name or array of them
	 * @param mixed $default The value to return if cache is missed
	 * @return mixed Cached value or the default
	 */
	public function fetch($key, $default = false) {
		return $default;
	}

	/**
	 * Removes cache entry by key.
	 *
	 * @param string $key Key of the cache entry to remove
	 * @return boolean Was removing the entry successful
	 */
	public function remove($key) {
		return true;
	}

	/**
	 * Clears the cache.
	 */
	public function clear() {}
    
    /**
     * Destroys the cache connection if opened.
     */
    public function destroy() {}
}
