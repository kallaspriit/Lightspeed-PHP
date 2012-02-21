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
 * Cache strategy using distributed memcached.
 */
class MemcachedCacheStrategy extends CacheStrategy {

	/**
	 * Memcached servers to use.
	 *
	 * The servers array should include the following for every server:
	 * - host
	 * - port
	 * - persistent
	 * - weight
	 * - connection_timeout
	 *
	 * @var array
	 */
	protected $servers;

	/**
	 * The memcache instance.
	 *
	 * This is lazy-loaded when first requested.
	 *
	 * @var Memcached
	 */
	protected $memcache;
	
	/**
	 * Local mapping of values so if a value is requested in the same request it
	 * was set, the value is returned from this rather than contacting the
	 * memcached service.
	 * 
	 * @var array
	 */
	protected $values = array();

	/**
	 * Constructs the application, optionally sets the servers to use.
	 *
	 * The servers array should include the following for every server:
	 * - host
	 * - port
	 * - persistent
	 * - weight
	 * - connection_timeout
	 * 
	 * @param array $servers Servers to use
	 */
	public function  __construct(array $servers = array()) {
		$this->servers = $servers;
	}
    
    /**
     * Close the memcached connection on destruction.
     */
    public function __destruct() {
        $this->destroy();
    }
    
    /**
     * Destroys the cache connection if opened.
     */
    public function destroy() {
        if ($this->memcache !== null) {
			$this->memcache = null;
        }
    }

	/**
	 * Returns the memcache handler.
	 *
	 * Lazy-loads it if not loaded already.
	 *
	 * @return Memcached
	 */
	public function getMemcache() {
		if ($this->memcache === null) {
			$this->memcache = new Memcached();
			
			$this->memcache->addServers($this->servers);
			
			$this->memcache->setOption(
				Memcached::OPT_BINARY_PROTOCOL,
				true
			);
			
			$this->memcache->setOption(
				Memcached::OPT_COMPRESSION,
				false
			);
			
			$this->memcache->setOption(
				Memcached::OPT_LIBKETAMA_COMPATIBLE,
				true
			);
			
			$this->memcache->setOption(
				Memcached::OPT_DISTRIBUTION,
				Memcached::DISTRIBUTION_CONSISTENT
			);
			
			$this->memcache->setOption(
				Memcached::OPT_NO_BLOCK,
				true
			);
			
			$this->memcache->setOption(
				Memcached::OPT_CONNECT_TIMEOUT,
				1000
			);
			
			$this->memcache->setOption(
				Memcached::OPT_CACHE_LOOKUPS,
				true
			);
			
			$this->memcache->setOption(
				Memcached::OPT_BUFFER_WRITES,
				true
			);
			
			//$this->memcache->setOption(
			//	Memcached::OPT_SERIALIZER,
			//	Memcached::SERIALIZER_IGBINARY
			//);
		}

		return $this->memcache;
	}

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
	public function store($key, $value, $timeToLiveSeconds = 0, $tags = null) {
		if (isset($tags)) {
			if (is_array($tags)) {
				$this->associateKeyWithTags($key, $tags, $timeToLiveSeconds);
			} else if (is_string($tags)) {
				$this->associateKeyWithTag($key, $tags, $timeToLiveSeconds);
			} else {
				throw new Exception(
					'Invalid tags given, expected null, a string or an array'
				);
			}
		}
		
		$this->values[$key] = $value;
		
		try {
			return $this->getMemcache()->set(
				$key,
				$value,
				$timeToLiveSeconds
			);
		} catch (Exception $e) {
			// usually fails when something is not serializable
			return false;
		}
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
		$memcache = $this->getMemcache();
		$data = null;
		
		if (is_array($key)) {
			$data = array();
			
			foreach ($key as $subKey) {
				if (array_key_exists($subKey, $this->values)) {
					$data[$subKey] = $this->values[$subKey];
				} else {
					$data = null;
					
					break;
				}
			}
			
			if (is_array($data)) {
				return $data;
			}
			
			$data = $this->getMemcache()->getMulti($key);
		} else {
			if (array_key_exists($key, $this->values)) {
				return $this->values[$key];
			}
			
			$data = $this->getMemcache()->get($key);
		}

		if ($this->memcache->getResultCode() == Memcached::RES_SUCCESS) {
			return $data;
		}

		return $default;
	}

	/**
	 * Removes cache entry by key.
	 *
	 * @param string $key Key of the cache entry to remove
	 * @return boolean Was removing the entry successful
	 */
	public function remove($key) {
		if (is_array($key)) {
			foreach ($key as $subKey) {
				if (!$this->remove($subKey)) {
					//@codeCoverageIgnoreStart
					return false;
					//@codeCoverageIgnoreEnd
				}
			}
			
			return true;
		}
		
		if (array_key_exists($key, $this->values)) {
			unset($this->values[$key]);
		}
		
		return $this->getMemcache()->delete($key, 0);
	}

	/**
	 * Clears the cache.
	 */
	public function clear() {
		$this->values = array();
		
		$this->getMemcache()->flush();
	}
}
