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
 * @subpackage View
 */

/**
 * A view renders the dynamic information put together by the controller using
 * services and models to a format that can be sent to the client.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage View
 */
class ViewBase {

	/**
	 * Data assigned to current view
	 * 
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Controller that crated the view.
	 *
	 * This might not always exist when the view was not updated in
	 * {@see Controller::onPreDispatch()} with the info.
	 *
	 * @var ControllerBase
	 */
	protected $_controller;

	/**
	 * Front controller used to create the view.
	 *
	 * This might not always exist when the view was not updated in
	 * {@see Controller::onPreDispatch()} with the info.
	 *
	 * @var FrontControllerBase
	 */
	protected $_frontController;

	/**
	 * Front controller used to create the view.
	 *
	 * This might not always exist when the view was not updated in
	 * {@see Controller::onPreDispatch()} with the info.
	 *
	 * @var HttpRequest
	 */
	protected $_request;

	/**
	 * Bootstrapper used to create the view.
	 *
	 * This might not always exist when the view was not updated in
	 * {@see Controller::onPreDispatch()} with the info.
	 *
	 * @var BootstrapperBase
	 */
	protected $_bootstrapper;

	/**
	 * Router used to create the view.
	 *
	 * This might not always exist when the view was not updated in
	 * {@see Controller::onPreDispatch()} with the info.
	 *
	 * @var RouterBase
	 */
	protected $_router;

	/**
	 * Dispatcher used to create the view.
	 *
	 * This might not always exist when the view was not updated in
	 * {@see Controller::onPreDispatch()} with the info.
	 *
	 * @var DispatcherBase
	 */
	protected $_dispatcher;

	/**
	 * Route used to create the view.
	 *
	 * This might not always exist when the view was not updated in
	 * {@see Controller::onPreDispatch()} with the info.
	 *
	 * @var RouteBase
	 */
	protected $_route;

	/**
	 * DispatchToken used to create the view.
	 *
	 * This might not always exist when the view was not updated in
	 * {@see Controller::onPreDispatch()} with the info.
	 *
	 * @var DispatchToken
	 */
	protected $_dispatchToken;
	
	/**
	 * Filename of the script to render by default.
	 * 
	 * @var string
	 */
	protected $_scriptFilename;

	/**
	 * Name of the currently processed cache block.
	 *
	 * @var string
	 */
	protected $_activeCacheBlock;

	/**
	 * Active cache block contect identifier
	 *
	 * @var string
	 */
	protected $_activeCacheContext;

	/**
	 * The block contents of currently processed cache block.
	 *
	 * @var string
	 */
	protected $_activeCacheBlockContents;

	/**
	 * Constructor, optionally sets the view script to render.
	 *
	 * @param string $scriptFilename Filename of the script to render.
	 */
	public function __construct($scriptFilename = null) {
		if ($scriptFilename !== null) {
			$this->setScript($scriptFilename);
		}
	}

	/**
	 * Sets the script filename to render by default.
	 *
	 * @param string $filename Filename of the script
	 */
	public function setScript($filename) {
		$this->_scriptFilename = $filename;
	}

	/**
	 * Returns the filename of the script to render by default.
	 *
	 * This is null by default, set in constructor or by using
	 * {@see ViewBase::setScript()}.
	 *
	 * @return string|null The filename of the script if available
	 */
	public function getScript() {
		return $this->_scriptFilename;
	}
	
	/**
	 * Sets a view variable.
	 *
	 * The magic method allows to set arbitrary view variables using simply
	 * $view->foo = 'bar'; etc. The values are stores in $_data field.
	 *
	 * @param string $name Name of the view variable to set
	 * @param mixed $value Value of any type to set
	 */
	public function __set($name, $value) {
		$this->_data[$name] = $value;
	}

	/**
	 * Returns a view variable by name.
	 *
	 * The magic method allows to get arbitrary view variables using simply
	 * echo $view->foo; etc. The values are stores in $_data field.
	 *
	 * Notice that in an effort not to let accidentally undefined variables go
	 * unnoticed, this method throws an exception if no variable with given
	 * name has been set.
	 *
	 * If at some point you can not be sure whether a variable has been set,
	 * use either isset() on it or the {@see ViewBase::exists()} method to check
	 * whether a variable exists.
	 *
	 * @param string $name Name of the view variable to get
	 * @return mixed Value of the variable or null if not set
	 * @throws Exception If requested view variable does not exist
	 */
	public function __get($name) {
		if (!array_key_exists($name, $this->_data)) {
			throw new Exception('View variable "'.$name.'" is not set');
		}

		return $this->_data[$name];
	}

	/**
	 * Magic method to check whether a view variable has been set.
	 *
	 * Notice that like normal isset(), this returns false if the variable has
	 * been set to NULL. Use {@see ViewBase::exists()} to check whether it has
	 * been set at all.
	 *
	 * @param string $name Name of the variable to check
	 * @return boolean Is the variable set
	 */
	public function  __isset($name) {
		return isset($this->_data[$name]);
	}

	/**
	 * Magic method to unset a view variable.
	 *
	 * Works just like normal unset but works on view variables.
	 *
	 * Does not throw a exception if variable does not exist.
	 *
	 * @param string $name Name of the variable to check
	 */
	public function  __unset($name) {
		unset($this->_data[$name]);
	}

	/**
	 * Returns whether a view variable exists.
	 *
	 * Returns true even if the value of it has been set to NULL.
	 *
	 * @param string $name Name of the variable to check
	 * @return boolean Does the view variable exist
	 */
	public function exists($name) {
		return array_key_exists($name, $this->_data);
	}

	/**
	 * Returns a view variable by name or a default value if does not exist.
	 *
	 * Notice that the default value is also returned if a view variable is set
	 * but to a NULL (isset() is used instead of array_key_exists()).
	 *
	 * @param string $name Name of the view variable to fet
	 * @param mixed $default Default value to return if variable is not set
	 * @return mixed Value of the variable or null if not set
	 */
	public function get($name, $default = null) {
		if (isset($this->_data[$name])) {
			return $this->_data[$name];
		}

		return $default;
	}

	/**
	 * Returns all view data.
	 *
	 * @return array
	 */
	public function getData() {
		return $this->_data;
	}

	/**
	 * Sets view data.
	 *
	 * Overwrites any existing data.
	 *
	 * @param array $data Data to use
	 */
	public function setData(array $data) {
		$this->_data = $data;
	}

	/**
	 * Sets controller
	 *
	 * @param ControllerBase $controller
	 */
	public function setController(ControllerBase $controller) {
		$this->_controller = $controller;
	}

	/**
	 * Returns controller
	 *
	 * @return ControllerBase
	 */
	public function getController() {
		return $this->_controller;
	}

	/**
	 * Sets front controller
	 *
	 * @param FrontControllerBase Front controller
	 */
	public function setFrontController(FrontControllerBase $frontController) {
		$this->_frontController = $frontController;
	}

	/**
	 * Returns front controller
	 *
	 * @return FrontControllerBase
	 */
	public function getFrontController() {
		return $this->_frontController;
	}

	/**
	 * Sets request
	 *
	 * @param HttpRequest $request
	 */
	public function setRequest(HttpRequest $request) {
		$this->_request = $request;
	}

	/**
	 * Returns request
	 *
	 * @return HttpRequest
	 */
	public function getRequest() {
		return $this->_request;
	}

	/**
	 * Sets bootstrapper
	 *
	 * @param BootstrapperBase $bootstrapper
	 */
	public function setBootstrapper(BootstrapperBase $bootstrapper) {
		$this->_bootstrapper = $bootstrapper;
	}

	/**
	 * Returns boostrapper
	 *
	 * @return BootstrapperBase
	 */
	public function getBootstapper() {
		return $this->_bootstrapper;
	}

	/**
	 * Sets router
	 *
	 * @param RouterBase $router
	 */
	public function setRouter(RouterBase $router) {
		$this->_router = $router;
	}

	/**
	 * Returns router
	 *
	 * @return RouterBase
	 */
	public function getRouter() {
		return $this->_router;
	}

	/**
	 * Sets dispatcher
	 *
	 * @param DispatcherBase $dispatcher
	 */
	public function setDispatcher(DispatcherBase $dispatcher) {
		$this->_dispatcher = $dispatcher;
	}

	/**
	 * Returns dispatcher
	 *
	 * @return DispatcherBase
	 */
	public function getDispatcher() {
		return $this->_dispatcher;
	}

	/**
	 * Sets route
	 *
	 * @param RouteBase $route
	 */
	public function setRoute(RouteBase $route) {
		$this->_route = $route;
	}

	/**
	 * Returns route
	 *
	 * @return RouteBase
	 */
	public function getRoute() {
		return $this->_route;
	}

	/**
	 * Sets dispatch token
	 *
	 * @param DispatchToken $dispatchToken
	 */
	public function setDispatchToken(DispatchToken $dispatchToken) {
		$this->_dispatchToken = $dispatchToken;
	}

	/**
	 * Returns dispatch token
	 *
	 * @return DispatchToken
	 */
	public function getDispatchToken() {
		return $this->_dispatchToken;
	}

	/**
	 * Compiles a url based on defined route and parameters.
	 *
	 * The parameters is optional and only needed for routes with variables. It
	 * is then expected to be an associative array with keys that match route
	 * variable names and values are replaced into the route instead of the
	 * appopriate variable definitions.
	 *
	 * If caching is enabled and $useCache true, if a route url with the same
	 * name and parameters has been requested before, the cached result is
	 * returned. Fast local cache is used for this.
	 *
	 * Route is generated in currently active language.
	 *
	 * @param string $routeName Name of the route to build on
	 * @param array $parameters Optional parameters to use as route variables
	 * @param boolean $useCache Should cache be used
	 * @param boolean $useTranslator Should translator be used if possible
	 * @throws Exception If route is not defined or passed invalid parameters
	 * @return string The compiled route url
	 * @throws Exception if route parameter or token translation does not exist
	 */
	public function makeUrl(
		$routeName,
		array $parameters = array(),
		$useCache = LS_USE_SYSTEM_CACHE,
		$useTranslator = true
	) {
		if (!isset($this->_router)) {
			throw new Exception(
				'Unable to create route, router has not been set'
			);
		}

		return $this->_router->makeUrl(
			$routeName,
			$parameters,
			$useCache,
			$useTranslator
		);
	}

	/**
	 * Renders a view script.
	 *
	 * @param string $filename The filename of the php view script
	 * @return <string Rendered view content
	 */
	public function render($filename = null) {
		if ($filename !== null) {
			$this->setScript($filename);
		}

		if ($this->_scriptFilename === null) {
			throw new Exception('View script filename has not been set');
		}

		if (!$this->viewFileExists($this->_scriptFilename)) {
			throw new Exception('View script "'.$filename.'" does not exist');
		}
		
		ob_start();

		// As unbelivable as this is, this does not work in HPHP without this
		$view = $this;

		require($this->_scriptFilename);

		return ob_get_clean();
	}
	
	
	/**
	 * Returns request parameter by name.
	 *
	 * Proxies the request to {@see HttpRequest::getParam()}. Also if $makeSafe
	 * is true, the value is passed through htmlspecialchars() to make it safe
	 * to display on the page / as value of an input.
	 *
	 * @param string $name Name of the parameter
	 * @param mixed $default Value to return if param is not set
	 * @param boolean $makeSafe Should the returned value be made safe
	 * @return string The parameter or null if not set
	 */
	protected function param($name, $default = null, $makeSafe = true) {
		$value = $this->_request->getParam($name, $default);

		if (is_string($value) && $makeSafe) {
			$value = htmlspecialchars($value);
		}

		return $value;
	}
	
	/**
	 * Returns whether the file for given controller view exists.
	 * 
	 * If system cache is enabled, uses cache to resolve this without file stat.
	 * 
	 * @param string $viewFilename Name of the controller view file
	 */
	protected function viewFileExists($viewFilename) {
		//@codeCoverageIgnoreStart
		if (!LS_USE_SYSTEM_CACHE) {
			return file_exists($viewFilename);
		}
		//@codeCoverageIgnoreEnd
		
		$cacheKey = 'lightspeed.view-file-exists|'.$viewFilename;
		$exists = Cache::fetchLocal($cacheKey, false);
		
		if ($exists === false) {
			$exists = file_exists($viewFilename) ? 1 : 0;
			
			Cache::storeLocal($cacheKey, $exists, LS_TTL_DISPATCH_RESOLVE);
		}
		
		return $exists == 1 ? true : false;
	}

	/**
	 * Begins a new cached block.
	 *
	 * The name of the cache block should be unique in given view and every
	 * cache block started needs to be ended with call to
	 * {@see ViewBase::endCacheBlock()}.
	 * 
	 * Call to this method should be in an if-statement and the contents of the
	 * statement should only be executed if this method returns true. The call
	 * to end cache block should follow right after the if.
	 *
	 * If context is not set, the one last set in the controller with call to
	 * {@see ControllerBase::isBlockCached()} is used.
	 *
	 * Cache blocks may not be nested.
	 * 
	 * @param string $name Name of the block to cache
	 * @param string|array $context Cache block context
	 * @return boolean Should the cachable block be executed
	 * @throws Exception If something goes very wrong
	 */
	protected function beginCacheBlock(
		$name,
		$context = null,
		$ttl = null
	) {
		if ($this->_activeCacheBlock !== null) {
			throw new Exception('Cache blocks can not be nested!');
		}

		$this->_activeCacheBlock = $name;

		if ($context !== null) {
			$this->_activeCacheContext = $context;
		} else {
			$this->_activeCacheContext = $this->getController()
				->getCurrentCacheBlockContext($this->_activeCacheBlock);
		}

		$blockId = $this->_activeCacheBlock.'.'.$this->_activeCacheContext;

//Debug::dump($blockId, 'Begin cache block', ROOT_PATH.'/cache.txt');

		$blockContents = null;

		$isCached = $this->getController()->isBlockCached(
			$this->_activeCacheBlock,
			$this->_activeCacheContext,
			$ttl,
			$blockContents
		);

		if ($isCached) {
			$this->_activeCacheBlockContents = $blockContents;
//Debug::dump('yes', 'Using cached block contents', ROOT_PATH.'/cache.txt');
			return false;
		} else {
//Debug::dump('no', 'Using cached block contents', ROOT_PATH.'/cache.txt');
			// start output-buffering to capture block contents
			ob_start();

			$this->_activeCacheBlockContents = null;

			return true;
		}
	}

	/**
	 * Ends currently active cache block.
	 *
	 * If during the last execution, the started block was not cached, this
	 * method extracts the output from output buffering and stores it in the
	 * cache. If it was cached, fetches the output from cache and output it.
	 *
	 * This method should be called AFTER the if-statement that included the
	 * beginning of the cache block.
	 *
	 * Outputs either the generated or cached block contents.
	 */
	protected function endCacheBlock() {
		if ($this->_activeCacheBlock === null) {
			throw new Exception('Unable to end cache block, none started');
		}

		$cacheBlockContents = null;

		if ($this->_activeCacheBlockContents !== null) {
//Debug::dump($this->_activeCacheBlockContents, 'Block is cached, using cached contents', ROOT_PATH.'/cache.txt');
			$cacheBlockContents = $this->_activeCacheBlockContents;
		} else {
			$cacheBlockContents = ob_get_clean();
			
			$blockId = $this->_activeCacheBlock.'.'.
				$this->_activeCacheContext;

			$contentCacheKey = 'lightspeed.cache-block-content|'.$blockId;

			$timeToLive = $this->getController()->getCacheBlockTTL(
				$this->_activeCacheBlock,
				$this->_activeCacheContext
			) + 1;
//Debug::dump($cacheBlockContents, 'Block not cached, storing it to "'.$contentCacheKey.'" for '.$timeToLive.' seconds', ROOT_PATH.'/cache.txt');
			Cache::storeLocal(
				$contentCacheKey,
				$cacheBlockContents,
				$timeToLive
			);

			$this->updateCacheBlockContextsCache(
				$this->_activeCacheBlock,
				$this->_activeCacheContext,
				$timeToLive
			);
		}

		$this->_activeCacheBlock = null;

		echo $cacheBlockContents;
	}

	/**
	 * Updates the list of contexts for given cache block.
	 *
	 * Adds given context to the list if not already on it.
	 *
	 * If the context list does not exists, it is created.
	 *
	 * @param string $blockName Name of the block
	 * @param string $context Context identifier
	 * @param integer $ttl How long to store the cache if first made
	 */
	protected function updateCacheBlockContextsCache(
		$blockName,
		$context,
		$ttl
	) {
		$blockContextesCacheKey = 'lightspeed.cache-block-context|'.
			$blockName;

		$blockContexts = Cache::fetchLocal($blockContextesCacheKey);
		$updateContexts = false;

		if ($blockContexts === false || !is_array($blockContexts)) {
			$blockContexts = array($context => time());
			$updateContexts = true;
		} else {
			if (!array_key_exists($context, $blockContexts)) {
				$blockContexts[$context] = time();
				$updateContexts = true;
			}
		}

		if ($updateContexts) {
			Cache::storeLocal(
				$blockContextesCacheKey,
				$blockContexts,
				$ttl
			);
		}
	}

}