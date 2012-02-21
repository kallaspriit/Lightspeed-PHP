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
 * Base class representing a cache strategy.
 */
abstract class CacheStrategy {
	
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
	abstract public function store($key, $value, $timeToLiveSeconds, $tags = null);

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
	abstract public function fetch($key, $default = false);

	/**
	 * Removes cache entry by key.
	 *
	 * @param string $key Key of the cache entry to remove
	 * @return boolean Was removing the entry successful
	 */
	abstract public function remove($key);

	/**
	 * Clears cache entries by tags.
	 *
	 * The tags should be either an array or comma-seperated string. A single
	 * tag is ofcourse also accepted.
	 *
	 * @param string|array $tags Tags to clear
	 */
	//public function clearBy($tags);

	/**
	 * Clears the cache.
	 */
	abstract public function clear();
    
    /**
     * Destroys the cache connection if opened.
     */
    abstract public function destroy();

	/**
	 * Associates given key to given tag.
	 *
	 * @param string $key Key to associate
	 * @param string $tag Tag to associate with
	 * @param integer $ttl How long to store the association
	 */
	public function associateKeyWithTag($key, $tag, $ttl) {
		$cacheKey = 'lightspeed.cache-tag-keys|'.$tag;

		$keys = $this->fetch($cacheKey, array());

		if (!in_array($key, $keys)) {
			$keys[] = $key;

			$this->store($cacheKey, $keys, $ttl);
		}
	}

	/**
	 * Associates given key to given tags.
	 *
	 * @param string $key Key to associate
	 * @param array $tags Tags to associate with
	 * @param integer $ttl How long to store the association
	 */
	public function associateKeyWithTags($key, array $tags, $ttl) {
		$cacheKeys = array();

		foreach ($tags as $tag) {
			$cacheKeys[$tag] = 'lightspeed.cache-tag-keys|'.$tag;
		}

		$tagKeys = $this->fetch($cacheKeys, array());

		foreach ($cacheKeys as $tag => $cacheKey) {
			$keys = array();

			if (array_key_exists($cacheKey, $tagKeys)) {
				$keys = $tagKeys[$cacheKey];
			}

			if (!in_array($key, $keys)) {
				$keys[] = $key;

				$this->store($cacheKey, $keys, $ttl);
			}
		}
	}

	/**
	 * Returns keys that have been tagged with given tag.
	 *
	 * @param string $tag Tags to get keys of
	 * @return array The array of tagged keys
	 */
	public function getKeysByTag($tag) {
		$cacheKey = 'lightspeed.cache-tag-keys|'.$tag;

		return $this->fetch($cacheKey, array());
	}

	/**
	 * Clears cache entries by tag.
	 *
	 * Clears all entries that were stored with given tag.
	 *
	 * @param string $tag Tag to clear by
	 * @return array Array of keys that were cleared
	 */
	public function clearByTag($tag) {
		$keys = $this->getKeysByTag($tag);

		$this->remove($keys);

		$cacheKey = 'lightspeed.cache-tag-keys|'.$tag;

		$this->remove($cacheKey);

		return $keys;
	}
}