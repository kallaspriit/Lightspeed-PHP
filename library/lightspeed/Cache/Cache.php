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

// Require used classes
require_once LIGHTSPEED_PATH.'/Cache/CacheStrategy.php';

/**
 * Enables local and remote caching of arbitrary data.
 *
 * Does not implement strategy pattern to easily switch the cache strategies
 * for efficiency.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Cache
 */
class Cache {

	/**
	 * Should local caching be used.
	 *
	 * @var boolean
	 */
	protected static $useLocalCache = false;
	/**
	 * Should global caching be used.
	 *
	 * @var boolean
	 */
	protected static $useGlobalCache = false;

	/**
	 * Local cache strategy.
	 *
	 * Values stored in local cache is only available on given machine.
	 *
	 * @var CacheStrategy
	 */
	protected static $localStrategy;

	/**
	 * Global cache strategy.
	 *
	 * Global cache should return the same data requested from any machine.
	 *
	 * @var CacheStrategy
	 */
	protected static $globalStrategy;

	/**
	 * Sets local cache strategy to use.
	 *
	 * @param CacheStrategy $strategy Strategy to use.
	 */
	public static function setLocalStrategy(CacheStrategy $strategy) {
		self::$localStrategy = $strategy;
		self::$useLocalCache = LS_USE_LOCAL_CACHE;
	}

	/**
	 * Returns strategy used for local cache.
	 *
	 * @return CacheStrategy
	 */
	public static function getLocalStrategy() {
		return self::$localStrategy;
	}

	/**
	 * Sets global cache strategy to use.
	 *
	 * @param CacheStrategy $strategy Strategy to use.
	 */
	public static function setGlobalStrategy(CacheStrategy $strategy) {
		self::$globalStrategy = $strategy;
		self::$useGlobalCache = LS_USE_GLOBAL_CACHE;
	}

	/**
	 * Returns strategy used for global cache.
	 *
	 * @return CacheStrategy
	 */
	public static function getGlobalStrategy() {
		return self::$globalStrategy;
	}

	/**
	 * Sets whether local cache is enabled
	 *
	 * @param boolean $cacheEnabled Is cache enabled
	 */
	public static function setLocalEnabled($cacheEnabled = true) {
		self::$useLocalCache = $cacheEnabled;
	}

	/**
	 * Returns whether local cache is enabled
	 *
	 * @return boolean
	 */
	public static function isLocalEnabled() {
		return self::$useLocalCache;
	}

	/**
	 * Sets whether global cache is enabled
	 *
	 * @param boolean $cacheEnabled Is cache enabled
	 */
	public static function setGlobalEnabled($cacheEnabled = true) {
		self::$useGlobalCache = $cacheEnabled;
	}

	/**
	 * Returns whether global cache is enabled
	 *
	 * @return boolean
	 */
	public static function isGlobalEnabled() {
		return self::$useGlobalCache;
	}

	/**
	 * Stores a value on local machine cache.
	 *
	 * Use the local cache for data that does not need to be shared between
	 * all the machines serving the application. For this purpuse, it is faster
	 * then mamcache that works over the network.
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
	public static function storeLocal(
		$key,
		$value,
		$timeToLiveSeconds = LS_TTL_DEFAULT,
		$tags = null
	) {
		if (!self::$useLocalCache) {
			return true; // return true without actually storing the value
		}

		return self::$localStrategy->store(
			$key,
			$value,
			$timeToLiveSeconds,
			$tags
		);
	}

	/**
	 * Stores a value on global cache.
	 *
	 * Use the global cache for data that needs to be accessible on all
	 * machines using the global cache. As it goes over the network, it is
	 * slower than local cache.
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
	public static function storeGlobal(
		$key,
		$value,
		$timeToLiveSeconds = LS_TTL_DEFAULT,
		$tags = null
	) {
		if (!self::$useGlobalCache) {
			return true; // return true without actually storing the value
		}

		return self::$globalStrategy->store(
			$key,
			$value,
			$timeToLiveSeconds,
			$tags
		);
	}

	/**
	 * Stores a value on cache of chosen strategy.
	 *
	 * Use the global cache for data that needs to be accessible on all
	 * machines using the global cache. As it goes over the network, it is
	 * slower than local cache.
	 *
	 * @param string $strategy Name of strategy, use LS_CACHE_.. constants
	 * @param string $key Key of the value to store
	 * @param mixed $value Value to store
	 * @param integer $timeToLiveSeconds How long to store the cache in seconds
	 * @return boolean Was storing the value successful
	 * @throws Exception If using invalid cache strategy
	 */
	public static function store(
		$strategy,
		$key,
		$value,
		$timeToLiveSeconds = LS_TTL_DEFAULT
	) {
		switch ($strategy) {
			case LS_CACHE_GLOBAL:
				return Cache::storeGlobal($key, $value, $timeToLiveSeconds);
			break;

			case LS_CACHE_LOCAL:
				return Cache::storeLocal($key, $value, $timeToLiveSeconds);
			break;

			default:
				throw new Exception(
					'Unsupported cache storage method "'.$strategy.'"'
				);
			break;
		}
	}

	/**
	 * Returns a value from local machine cache.
	 *
	 * If no cache match is found, the value defined in $default is returned.
	 *
	 * If an array of cache keys is given, an array containiCache::fetchLocal('foo');ng the results is
	 * returned with the requested keys as array keys. If some key is not found,
	 * it is not returned back.
	 *
	 * If constant LS_USE_LOCAL_CACHE is set to false, the function always
	 * returns the default value and does not attempt to read the cache.
	 *
	 * Use the local cache for data that does not need to be shared between
	 * all the machines serving the application. For this purpuse, it is faster
	 * then mamcache that works over the network.
	 *
	 * @param string|array $key Cached data key name or array of them
	 * @param mixed $default The value to return if cache is missed
	 * @return mixed Cached value or the default
	 */
	public static function fetchLocal($key, $default = false) {
		if (is_array($key) && $default !== false) {
			throw new Exception(
				'Using a default value when fetching '.
				'multiple keys is not allowed'
			);
		}

		if (!self::$useLocalCache) {
			return $default;
		}
		
		if (self::$localStrategy === null) {
			throw new Exception("Local cache strategy not set");
		}

		return self::$localStrategy->fetch($key, $default);
	}

	/**
	 * Returns a value from global cache.
	 *
	 * If no cache match is found, the value defined in $default is returned.
	 *
	 * If an array of cache keys is given, an array containing the results is
	 * returned with the requested keys as array keys. If some key is not found,
	 * it is not returned back.
	 *
	 * If constant LS_USE_GLOBAL_CACHE is set to false, the function always
	 * returns the default value and does not attempt to read the cache.
	 *
	 * Use the global cache for data that needs to be accessible on all
	 * machines using the global cache. As it goes over the network, it is
	 * slower than local cache.
	 *
	 * @param string|array $key Cached data key name or array of them
	 * @param mixed $default The value to return if cache is missed
	 * @return mixed Cached value or the default
	 */
	public static function fetchGlobal($key, $default = false) {
		if (is_array($key) && $default !== false) {
			throw new Exception(
				'Using a default value when fetching '.
				'multiple keys is not allowed'
			);
		}

		if (!self::$useGlobalCache) {
			return $default;
		}
		
		if (self::$globalStrategy === null) {
			throw new Exception("Global cache strategy not set");
		}

		return self::$globalStrategy->fetch($key, $default);
	}

	/**
	 * Returns a value from cache of given strategy.
	 *
	 * If no cache match is found, the value defined in $default is returned.
	 *
	 * If an array of cache keys is given, an array containing the results is
	 * returned with the requested keys as array keys. If some key is not found,
	 * it is not returned back.
	 *
	 * Use the global cache for data that needs to be accessible on all
	 * machines using the global cache. As it goes over the network, it is
	 * slower than local cache.
	 *
	 * @param string $strategy Name of strategy, use LS_CACHE_.. constants
	 * @param string|array $key Cached data key name or array of them
	 * @param mixed $default The value to return if cache is missed
	 * @return mixed Cached value or the default
	 * @throws Exception If using invalid cache strategy
	 */
	public static function fetch($strategy, $key, $default = false) {
		switch ($strategy) {
			case LS_CACHE_GLOBAL:
				return Cache::fetchGlobal($key, $default);
			break;

			case LS_CACHE_LOCAL:
				return Cache::fetchLocal($key, $default);
			break;

			default:
				throw new Exception(
					'Unsupported cache storage method "'.$strategy.'"'
				);
			break;
		}
	}

	/**
	 * Removes local cache entry by key.
	 *
	 * @param string $key Key of the cache entry to remove
	 * @return boolean Was removing the entry successful
	 */
	public static function removeLocal($key) {
		if (!self::$useLocalCache) {
			return true;
		}

		return self::$localStrategy->remove($key);
	}

	/**
	 * Removes global cache entry by key.
	 *
	 * @param string $key Key of the cache entry to remove
	 * @return boolean Was removing the entry successful
	 */
	public static function removeGlobal($key) {
		if (!self::$useGlobalCache) {
			return true;
		}

		return self::$globalStrategy->remove($key);
	}

	/**
	 * Removes cache entry by key from given strategy.
	 *
	 * @param string $strategy Name of strategy, use LS_CACHE_.. constants
	 * @param string $key Key of the cache entry to remove
	 * @return boolean Was removing the entry successful
	 * @throws Exception If using invalid cache strategy
	 */
	public static function remove($strategy, $key) {
		switch ($strategy) {
			case LS_CACHE_GLOBAL:
				return Cache::removeGlobal($key);
			break;

			case LS_CACHE_LOCAL:
				return Cache::removeLocal($key);
			break;

			default:
				throw new Exception(
					'Unsupported cache storage method "'.$strategy.'"'
				);
			break;
		}
	}

	/**
	 * Clears cache entries by tag.
	 *
	 * Clears all entries that were stored with given tag.
	 *
	 * @param string $tag Tag to clear by
	 * @return array Array of keys that were cleared
	 */
	public function clearByTagLocal($tag) {
		return self::$localStrategy->clearByTag($tag);
	}

	/**
	 * Clears cache entries by tag.
	 *
	 * Clears all entries that were stored with given tag.
	 *
	 * @param string $tag Tag to clear by
	 * @return array Array of keys that were cleared
	 */
	public function clearByTagGlobal($tag) {
		return self::$globalStrategy->clearByTag($tag);
	}

	/**
	 * Clears local cache.
	 */
	public static function clearLocal() {
		if (!self::$useLocalCache) {
			return;
		}

		self::$localStrategy->clear();
	}

	/**
	 * Clears global cache.
	 *
	 * Notice that this clears the whole global cache which may greatly increase
	 * the load on cached resources like databases.
	 */
	public static function clearGlobal() {
		if (!self::$useGlobalCache) {
			return;
		}

		self::$globalStrategy->clear();
	}

	/**
	 * Clears cache of given strategy.
	 *
	 * Notice that this clears the whole cache which may greatly increase
	 * the load on cached resources like databases.
	 *
	 * @param string $strategy Name of strategy, use LS_CACHE_.. constants
	 */
	public static function clear($strategy) {
		switch ($strategy) {
			case LS_CACHE_GLOBAL:
				Cache::clearGlobal();
			break;

			case LS_CACHE_LOCAL:
				Cache::clearLocal();
			break;

			default:
				throw new Exception(
					'Unsupported cache storage method "'.$strategy.'"'
				);
			break;
		}
	}
	
	/**
	 * Destroys the cache strategy connections and sets the local and global
	 * cache strategies to nulls and using of local and global cache to false.
	 */
	public static function destroy() {
		if (self::$localStrategy !== null) {
			self::$localStrategy->destroy();
			self::$localStrategy = null;
			self::$useLocalCache = false;
		}
		
		if (self::$globalStrategy !== null) {
			self::$globalStrategy->destroy();
			self::$globalStrategy = null;
			self::$useGlobalCache = false;
		}
	}

}