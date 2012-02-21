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
 * @subpackage Controller
 */

// Include all the classes it depends on
require_once LIGHTSPEED_PATH.'/Controller/FrontControllerBase.php';
require_once LIGHTSPEED_PATH.'/Http/HttpRequest.php';
require_once LIGHTSPEED_PATH.'/Http/HttpResponse.php';
require_once LIGHTSPEED_PATH.'/Bootstrapper/BootstrapperBase.php';
require_once LIGHTSPEED_PATH.'/Router/RouterBase.php';
require_once LIGHTSPEED_PATH.'/Router/RouteBase.php';
require_once LIGHTSPEED_PATH.'/Dispatcher/DispatcherBase.php';
require_once LIGHTSPEED_PATH.'/Dispatcher/DispatchToken.php';
require_once LIGHTSPEED_PATH.'/View/ViewBase.php';

/**
 * The very base functionality of a controller. You should extend it with a
 * controller class specifiyng base controller logic for your application
 * that your action controllers extend.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Controller
 */
class ControllerBase {

	/**
	 * Was the controller action called by front controller dispatched or not.
	 * 
	 * This is set to true by default by the
	 * {@see ControllerBase::onPreDispatch()} method called by the front
	 * controller just before the action method is called and back to false by
	 * {@see ControllerBase::onPostDispatch()} called after action method.
	 * 
	 * It can be used to check in your action whether it was called normally by
	 * the dispatching process and thus all the fields about front controller,
	 * request etc are set or perhaps by simply instatsiating a controller and
	 * calling the action directly.
	 * 
	 * @var boolean
	 */
	protected $dispatched = false;

	/**
	 * Front controller that dispatched the call to this controller.
	 *
	 * This is normally only available if a controller action was called by
	 * the front controller and {@see ControllerBase::onPreDispatch()} was
	 * called. The {@see ControllerBase::onPostDispatch()} sets it back to null.
	 *
	 * @var FrontControllerBase
	 */
	protected $frontController;

	/**
	 * The initial http request that was sent to the application.
	 *
	 * This is normally only available if a controller action was called by
	 * the front controller and {@see ControllerBase::onPreDispatch()} was
	 * called. The {@see ControllerBase::onPostDispatch()} sets it back to null.
	 *
	 * @var HttpRequest
	 */
	protected $request;

	/**
	 * The bootstrapper that bootstrapped the application.
	 *
	 * This is normally only available if a controller action was called by
	 * the front controller and {@see ControllerBase::onPreDispatch()} was
	 * called. The {@see ControllerBase::onPostDispatch()} sets it back to null.
	 *
	 * @var BootstrapperBase
	 */
	protected $bootstrapper;

	/**
	 * The router used to handle the request.
	 *
	 * This is normally only available if a controller action was called by
	 * the front controller and {@see ControllerBase::onPreDispatch()} was
	 * called. The {@see ControllerBase::onPostDispatch()} sets it back to null.
	 *
	 * @var RouterBase
	 */
	protected $router;

	/**
	 * The dispatcher used to dispatch the request.
	 *
	 * This is normally only available if a controller action was called by
	 * the front controller and {@see ControllerBase::onPreDispatch()} was
	 * called. The {@see ControllerBase::onPostDispatch()} sets it back to null.
	 *
	 * @var DispatcherBase
	 */
	protected $dispatcher;

	/**
	 * The initial route that was matched.
	 *
	 * This is normally only available if a controller action was called by
	 * the front controller and {@see ControllerBase::onPreDispatch()} was
	 * called. The {@see ControllerBase::onPostDispatch()} sets it back to null.
	 *
	 * @var RouteBase
	 */
	protected $route;

	/**
	 * The dispatch token that was dispatched to this controller.
	 *
	 * This is normally only available if a controller action was called by
	 * the front controller and {@see ControllerBase::onPreDispatch()} was
	 * called. The {@see ControllerBase::onPostDispatch()} sets it back to null.
	 *
	 * @var DispatchToken
	 */
	protected $dispatchToken;

	/**
	 * The response instance.
	 *
	 * The response instance is created once at the beginning of the dispatch
	 * loop and may be modified by any of the controllers that the dispatch
	 * sequence will visit.
	 *
	 * This is normally only available if a controller action was called by
	 * the front controller and {@see ControllerBase::onPreDispatch()} was
	 * called. The {@see ControllerBase::onPostDispatch()} sets it back to null.
	 *
	 * @var HttpResponse
	 */
	protected $response;

	/**
	 * Dispatch token where to forward the request.
	 *
	 * @var DispatchToken
	 */
	protected $forward;

	/**
	 * Script name of the layout script to render in post-dispatch method of
	 * {@see Controller::onPostDispatch()} around the rendered view.
	 *
	 * You may override this in your action method by calling
	 * {@see Controller::setLayout()}.
	 *
	 * @var string
	 */
	protected $layoutScriptName = 'default';

	/**
	 * Filename of the view script to render in post-dispatch method of
	 * {@see Controller::onPostDispatch()}.
	 *
	 * This is decided by default in {@see Controller::onPreDispatch()} but
	 * you may override it in your action method by calling
	 * {@see Controller::setView()}.
	 *
	 * @var string
	 */
	protected $viewScriptFilename;

	/**
	 * The layout to render around the view.
	 *
	 * @var ViewBase
	 */
	protected $layout;

	/**
	 * The view to render as an action of actions.
	 *
	 * @var ViewBase
	 */
	protected $view;

	/**
	 * Is using layout disabled.
	 *
	 * @var boolean
	 */
	protected $layoutDisabled = false;

	/**
	 * Is using view disabled.
	 *
	 * @var boolean
	 */
	protected $viewDisabled = false;

	/**
	 * The time-to-live seconds of cache blocks.
	 *
	 * The keys are composed of cache block name and context imploded by
	 * fullstops.
	 *
	 * @var array
	 */
	protected $cacheBlockTTL = array();

	/**
	 * Cached cache block contents.
	 *
	 * The keys include the method name, context id and block name concatenated
	 * by fullstops. Used to cache the cache block contents in case of several
	 * requests.
	 *
	 * @var array
	 */
	protected $cacheBlockContents = array();

	/**
	 * Holds the current cache block context identifier.
	 *
	 * This is used by the view to get cache context of blocks that was already
	 * defined in controller so there is no need to set it again in view.
	 *
	 * The keys are cache block names and values the context identifiers.
	 *
	 * @var array
	 */
	protected $cacheCurrentContext = array();

	/**
	 * Constructs the controller.
	 *
	 * If you override the constructor in your controller implementation, make
	 * sure to call this parent constructor too or you won't have view and
	 * layout set up.
	 */
	public function  __construct() {
		$this->layout = new ViewBase();
		$this->view = new ViewBase();
	}

	/**
	 * Forwards processing the request to a next controller action.
	 *
	 * Uses the dispatcher and is only available if the action was dispatched
	 * through the front controller and field {@see ControllerBase::$dispatcher}
	 * is set.
	 *
	 * The actual forwarding occurs in the
	 * {@see ControllerBase::onPostDispatch()} method that, if forwarding is
	 * requested, returns a new {@see DispatchToken} that is catched by the
	 * front controller {@see FrontController::dispatch()} method and then
	 * dispatched again.
	 *
	 * If you return the call to this method or after it, current action method
	 * invocation is stopped, everything outputted so far is still appended to
	 * the response by default. If you call it without returning, current action
	 * will complete it's actions before the handling is forwarded.
	 *
	 * Routes format means that controller and action names should be in format
	 * using lowercase characters and dashes, for example "user-manager" that is
	 * translated to controller name "UserManagerController" etc.
	 *
	 * @param string $controller Controller name in routes format
	 * @param string $action Action name in routes format
	 * @param array $parameters Request parameters
	 * @throws Exception If dispatcher field is not set to a dispatcher instance
	 */
	public function forward(
		$controller,
		$action = 'index',
		array $parameters = array()
	) {
		if (!isset($this->dispatcher)) {
			throw new Exception(
				'Unable to forward request, missing dispatcher'
			);
		}

		$this->forward = $this->dispatcher->build(
			$controller,
			$action,
			$parameters
		);
	}

	/**
	 * Redirects to given route.
	 *
	 * The route name is the same you would pass to {@see Router::makeUrl()}.
	 *
	 * @param string $routeName The route name
	 * @param array $parameters Route parameters
	 */
	//@codeCoverageIgnoreStart
	public function redirect($routeName, array $parameters = array()) {
		$url = $this->router->makeUrl($routeName, $parameters);

		header('location: '.$url);

		exit(0);
	}
	//@codeCoverageIgnoreEnd

	/**
	 * Sets non-default view script to use.
	 *
	 * Generally you don't have to call this as view script is determined from
	 * the route that matched the action.
	 *
	 * The controller and action name are expected in route format meaning
	 * using dashes lowercase names, for example "user-manager" controller
	 * name for "UserManagerController".
	 *
	 * The controller and action names are combined to a filename of the view
	 * script.
	 *
	 * @param string $controller The route-format controller name
	 * @param string $action The route-format action name
	 */
	protected function setView($controller, $action) {
		$this->viewScriptFilename = VIEW_PATH.'/'.$controller.
			'/'.$action.'.php';
	}
	
	/**
	 * Returns current view
	 *
	 * @return view
	 */	
	public function getView() {
		return $this->view;
	}

	/**
	 * Sets layout script name.
	 *
	 * Layout defaults to "default'.
	 *
	 * @param string $scriptName Layout script name to use.
	 */
	protected function setLayout($scriptName) {
		$this->layoutScriptName = $scriptName;
	}

	/**
	 * Disabled using a view.
	 *
	 * A layout is still rendered, also call {@see Controller::disableLayout()}
	 * to disable that.
	 *
	 * Unsets the view object so don't try to set any view variables after
	 * calling this.
	 */
	protected function disableView() {
		$this->viewDisabled = true;
	}

	/**
	 * Disabled using a layout.
	 *
	 * A view is still rendered, also call {@see Controller::disableView()}
	 * to disable that.
	 *
	 * Unsets the layout object so don't try to set any view variables after
	 * calling this.
	 */
	protected function disableLayout() {
		$this->layoutDisabled = true;
	}

	/**
	 * This method is called by the front controller just before a controller
	 * action method is called and includes all the information that that was
	 * gathered during the request.
	 *
	 * Starts output buffering.
	 *
	 * If this returns false, the dispatching to actual controller action method
	 * is skipped but {@see ControllerBase::onPostDispatch()} is still called
	 * which may return a new {@see DispatchToken} thus forwarding the entire
	 * request to another controller action.
	 *
	 * @param FrontControllerBase $frontController Triggering front-controller
	 * @param HttpRequest $request The initial request
	 * @param BootstrapperBase $bootstrapper Application bootstrapper
	 * @param RouterBase $router The router used to route the request
	 * @param DispatcherBase $dispatcher The dispatcher used for the request
	 * @param RouteBase $route Initially matched route
	 * @param DispatchToken $dispatchToken Dispatch token that led to this
	 * @param HttpResponse $response Response that is may modify
	 * @return boolean Should the request be dispatched
	 */
	public function onPreDispatch(
		FrontControllerBase $frontController,
		HttpRequest $request,
		BootstrapperBase $bootstrapper,
		RouterBase $router,
		DispatcherBase $dispatcher,
		RouteBase $route,
		DispatchToken $dispatchToken,
		HttpResponse $response
	) {
		$this->dispatched = true;
		$this->frontController = $frontController;
		$this->request = $request;
		$this->bootstrapper = $bootstrapper;
		$this->router = $router;
		$this->dispatcher = $dispatcher;
		$this->route = $route;
		$this->dispatchToken = $dispatchToken;
		$this->response = $response;

		$this->viewScriptFilename = VIEW_PATH.'/'.
			$dispatchToken->getControllerName().'/'.
			$dispatchToken->getActionName().'.php';

		$this->layout->setController($this);
		$this->layout->setFrontController($frontController);
		$this->layout->setRequest($request);
		$this->layout->setBootstrapper($bootstrapper);
		$this->layout->setRouter($router);
		$this->layout->setDispatcher($dispatcher);
		$this->layout->setRoute($route);
		$this->layout->setDispatchToken($dispatchToken);

		$this->view->setController($this);
		$this->view->setFrontController($frontController);
		$this->view->setRequest($request);
		$this->view->setBootstrapper($bootstrapper);
		$this->view->setRouter($router);
		$this->view->setDispatcher($dispatcher);
		$this->view->setRoute($route);
		$this->view->setDispatchToken($dispatchToken);

		ob_start();
		
		$this->setup();

		return true;
	}
	
	/**
	 * This is called before a controller action is called and can be used to
	 * perform any setup tasks such as choosing which layout to use.
	 */
	protected function setup() {}

	/**
	 * This method is called by the front controller just after a controller
	 * action method was called.
	 *
	 * You can override this in your derived controller implementation to do
	 * something more to just unset the data in controller and append the
	 * outputted content to the response. For example, it may decide not to
	 * append the action generated content to response.
	 *
	 * If forward fields has been set, it returns and unsets it.
	 *
	 * @return DispatchToken|null New dispatch token to forward request/null
	 */
	public function onPostDispatch() {
		$viewContent = null;
		$renderedContent = null;

		if (!isset($this->forward)) {
			// render the view if enabled
			if (isset($this->view) && !$this->viewDisabled) {
				$viewContent = $this->view->render($this->viewScriptFilename);
			}

			// pass the view content to layout and render it (if enabled)
			if (isset($this->layout) && !$this->layoutDisabled) {
				$layoutFilename = LAYOUT_PATH.'/'.$this->layoutScriptName.'.php';
				$this->layout->content = $viewContent;
				$renderedContent = $this->layout->render($layoutFilename);
			} else {
				$renderedContent = $viewContent;
			}

			// append the rendered layout and view content to response
			if (isset($renderedContent)) {
				$this->response->append($renderedContent);
			}
		}

		$output = ob_get_clean();

		$this->response->append($output);

		unset($this->frontController);
		unset($this->request);
		unset($this->bootstrapper);
		unset($this->dispatcher);
		unset($this->route);
		unset($this->dispatchToken);
		unset($this->response);

		$this->dispatched = false;
		$this->viewDisabled = false;
		$this->layoutDisabled = false;

		if (isset($this->forward)) {
			$forward = $this->forward;
			
			unset($this->forward);

			return $forward;
		}

		return null;
	}

	/**
	 * Returns whether given view cache block is cached.
	 *
	 * The cache context identifier $context is used to provide different cache
	 * content for different states. For example the context id might be the
	 * page number in case the controller action provides paginated content
	 * (the cache for each page should be different). It might also include
	 * language, whether the user has logged in etc. If array is provided, it
	 * will be serialized internally.
	 *
	 * If the block is cached, the contents of it will be placed in the
	 * by-reference $contents variable.
	 *
	 * @param string $blockName The name of the block to check
	 * @param string|array $context Cache context identifier
	 * @param integer $timeToLiveSeconds Number of seconds the block should be cached
	 * @param string &$contents Block contents if cached
	 * @return boolean Is the block cached
	 */
	public function isBlockCached(
		$blockName,
		$context = null,
		$timeToLiveSeconds = null,
		&$contents = null
	) {
		if (is_array($context)) {
			$context = serialize($context);
		}

		$blockId = $blockName.'.'.$context;

		$this->cacheCurrentContext[$blockName] = $context;

		if ($timeToLiveSeconds !== null) {
			$this->cacheBlockTTL[$blockId] = $timeToLiveSeconds;
		}

		$contents = $this->getCachedBlockContents(
			$blockName,
			$context
		);

		if ($contents === null) {
//Debug::dump('Block "'.$blockName.'" is not cached', 'Cache info', ROOT_PATH.'/cache.txt');
			return false;
		} else {
//Debug::dump($contents, 'Block "'.$blockName.'" is cached', ROOT_PATH.'/cache.txt');
			return true;
		}
	}

	/**
	 * Returns current cache context set with last call to isBlockCached().
	 *
	 * The cache context identifier $context is used to provide different cache
	 * content for different states. For example the context id might be the
	 * page number in case the controller action provides paginated content
	 * (the cache for each page should be different). It might also include
	 * language, whether the user has logged in etc.
	 *
	 * If no context has been set for given cache block, null is returned.
	 *
	 * @param string $blockName Name of the block to get info about
	 * @return string Current cache context id
	 */
	public function getCurrentCacheBlockContext($blockName) {
		if (array_key_exists($blockName, $this->cacheCurrentContext)) {
			return $this->cacheCurrentContext[$blockName];
		}

		return null;
	}
	

	/**
	 * Returns the time-to-live seconds of a cachable block by block name and
	 * context identifier.
	 *
	 * @param string $blockName Name of the cache block
	 * @param string $context Block context identifier
	 * @return integer Block Time to live in seconds, default if not set
	 */
	public function getCacheBlockTTL($blockName, $context = null) {
		$blockId = $blockName.'.'.$context;

		if (isset($this->cacheBlockTTL[$blockId])) {
			return $this->cacheBlockTTL[$blockId];
		}

		return LS_TTL_DEFAULT;
	}

	/**
	 * Returns cached block contents by method, state and block name.
	 *
	 * @param string $blockName Name of the block
	 * @param string $context Method context identifier
	 * @return string|null Block contents or null if not available
	 */
	public function getCachedBlockContents($blockName, $context) {
		$blockId = $blockName.'.'.$context;

		if (!array_key_exists($blockId, $this->cacheBlockContents)) {
			$cacheKey = 'lightspeed.cache-block-content|'.$blockId;

			$contents = Cache::fetchLocal($cacheKey);

			if ($contents === false) {
				return null;
			}

			$this->cacheBlockContents[$blockId] = $contents;
		}

		return $this->cacheBlockContents[$blockId];
	}

	/**
	 * Returns the list of contexts defined for given block.
	 *
	 * An empty array is returned if no contexts have been defined for given
	 * cache block.
	 * 
	 * @param string $blockName Name of the block
	 * @return array Array of context identifiers
	 */
	public function getCacheBlockContexts($blockName) {
		$cacheKey = 'lightspeed.cache-block-context|'.$blockName;

		$contexts = Cache::fetchLocal($cacheKey);

		if (!is_array($contexts)) {
			return array();
		}

		return $contexts;
	}

	/**
	 * Clears the cache of all contexts of given cache block.
	 *
	 * @param string $blockName Name of the cache block to clear.
	 * @return integer The number of contexts that were cleared
	 */
	public function clearBlockCache($blockName) {
		$contexts = $this->getCacheBlockContexts($blockName);
		$contextsCleared = 0;

		if (is_array($contexts) && !empty($contexts)) {
			foreach ($contexts as $context => $time) {
				if ($this->clearBlockCacheContext($blockName, $context)) {
					$contextsCleared++;
				}
			}
		}

		$cacheKey = 'lightspeed.cache-block-context|'.$blockName;

		Cache::removeLocal($cacheKey);

		return $contextsCleared;
	}

	/**
	 * Clears given cache block context.
	 *
	 * @param string $blockName Name of the cache block
	 * @param string $context Cache context identifier
	 * @return boolean Was removing the context successful
	 */
	public function clearBlockCacheContext($blockName, $context) {
		$blockId = $blockName.'.'.$context;
		
		$cacheKey = 'lightspeed.cache-block-content|'.$blockId;
//Debug::dump($cacheKey);
		return Cache::removeLocal($cacheKey);
	}

}