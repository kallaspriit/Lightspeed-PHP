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
 * @package Application
 * @subpackage Bootstrapper
 */

// We want to be able to autoload some classes
require_once APPLICATION_PATH.'/Autoload.php';

// Require base implementation
require_once LIGHTSPEED_PATH.'/Bootstrapper/BootstrapperBase.php';

// Get the translator, it is not required to use it but route token translation
// only works if this class exists
require_once LIGHTSPEED_PATH.'/Translator/Translator.php';

// Also use the debugging helper
require_once LIGHTSPEED_PATH.'/Debug/Debug.php';

// We are going to use application controllers
require_once APPLICATION_PATH.'/Controller.php';

// Get the cache strategies used
require_once LIGHTSPEED_PATH.'/Cache/Strategies/ApcCacheStrategy.php';

// use dummy cache instead if you don't want to setup APC
//require_once LIGHTSPEED_PATH.'/Cache/Strategies/DummyCacheStrategy.php';

// Get session and strategy
require_once LIGHTSPEED_PATH.'/Session/Session.php';
require_once LIGHTSPEED_PATH.'/Session/Strategies/CacheSessionStrategy.php';

// Get used entities
require_once APPLICATION_PATH.'/entities/Language.php';

/**
 * Bootsraps the application, initializing all required resources
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Application
 * @subpackage Bootstrapper
 */
class Bootstrapper extends BootstrapperBase {

	/**
	 * The apc cache handler used locally.
	 *
	 * @var ApcCacheHandler
	 */
	private $cacheStrategy;
	
	/**
	 * Session strategy.
	 * 
	 * @var SessionStrategy 
	 */
	private $sessionStrategy;
	
	/**
	 * Holds reference to the last instance of this class.
	 * 
	 * @var Bootstrapper 
	 */
	static private $instance;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		self::$instance = $this;
	}
	
	/**
	 * Returns last created instance of this class.
	 */
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
    
	/**
	 * Returns the current cache strategy.
	 *
	 * @return CacheHandler
	 */
	public function getCacheStrategy() {
		return $this->cacheStrategy;
	}
    
	/**
	 * Returns the current session strategy.
	 *
	 * @return SessionStrategy
	 */
	public function getSessionStrategy() {
		return $this->sessionStrategy;
	}
	
    /**
     * Called when the request has been handled.
     * 
     * @param HttpResponse $response The response about to be sent
     */
    public function onRequestComplete(HttpResponse $response) {

    }

	/**
	 * Bootstraps the application.
	 * 
	 * All the various parts of initialization can be placed in seperate private
	 * methods that should be listed in this method. It is automatically called
	 * when the application is bootstrapped.
	 * 
	 * If you wish to change they way base is initialized, override the
	 * {@see BootstrapperBase::bootstrapLightspeed()} method.
	 *
	 * @param HttpRequest $request The request
	 */
	protected function bootstrapApplication(HttpRequest $request) {
		$this->initCache();
		$this->initSession();
		$this->initMainTranslator();
		$this->initRoutesTranslator();
	}

	/**
	 * Sets up cache.
	 */
	private function initCache() {
		$this->cacheStrategy = new ApcCacheStrategy();

		Cache::setLocalStrategy($this->cacheStrategy);
		Cache::setGlobalStrategy($this->cacheStrategy);
	}

	/**
	 * Initializes session.
	 *
	 * Notice that in a multiserver setup where any query is load-balanced
	 * to hit a random web server, you should use a global cache method
	 * such as memcached. In a single-server setup a faster APC implementation
	 * can be useful.
	 */
	private function initSession() {
		$this->sessionStrategy = new CacheSessionStrategy($this->cacheStrategy);

		Session::setStrategy($this->sessionStrategy);
	}
	
	/**
	 * Initiates the main translator.
	 *
	 * Translations are loaded from translations file in
	 * application/translations/translations.php.
	 */
	private function initMainTranslator() {
		$translator = Translator::getInstance('main');
		$cacheKey = 'translations.main';
		
		$_translations = LS_USE_SYSTEM_CACHE
			? Cache::fetchLocal($cacheKey)
			: false;

		if ($_translations === false) {
			// the real $_translations array is defined in this file
			require TRANSLATIONS_PATH.'/main-translations.php';

			Cache::storeLocal($cacheKey, $_translations);
		}

		$translator->setLanguage(Language::get());
		$translator->setTranslations($_translations);
	}

	/**
	 * Initiates the routes translator.
	 *
	 * Translations are loaded from translations file in
	 * application/translations/routes.php
	 */
	private function initRoutesTranslator() {
		$translator = Translator::getInstance('routes');
		$cacheKey = 'translations.routes';
		$_translations = LS_USE_SYSTEM_CACHE
			? Cache::fetchLocal($cacheKey)
			: false;

		if ($_translations === false) {
			// the real $_translations array is defined in this file
			require TRANSLATIONS_PATH.'/route-translations.php';

			Cache::storeLocal($cacheKey, $_translations);
		}

		$translator->setLanguage(Language::get());
		$translator->setTranslations($_translations);
	}
}