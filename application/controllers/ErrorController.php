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
 * @subpackage Controllers
 */

/**
 * Error controller handles common errors that may occur in the system.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Application
 * @subpackage Controllers
 */
class ErrorController extends Controller {
	
	public function setup() {
		Cache::clearLocal();
		
		// dont show the layout in debug mode
		if (LS_DEBUG) {
			$this->disableLayout();
		}
	}

	public function pageNotFoundAction(array $params) {
		$this->view->request = $params['request'];
		
		$this->response->setResponseCode(404);
	}
	
	public function invalidControllerAction(array $params) {
		// show the generic error page when not in debug mode
		if (!LS_DEBUG) {
			return $this->forward('error', 'application-error', $params);
		}
		
		$this->view->request = $params['request'];
		$this->view->dispatchToken = $params['dispatch-token'];
		$this->view->exception = $params['exception'];
		
		$this->response->setResponseCode(404);
	}
	
	public function invalidActionAction(array $params) {
		// show the generic error page when not in debug mode
		if (!LS_DEBUG) {
			return $this->forward('error', 'application-error', $params);
		}
		
		$this->view->request = $params['request'];
		$this->view->dispatchToken = $params['dispatch-token'];
		
		$this->response->setResponseCode(404);
	}
	
	public function applicationErrorAction(array $params) {
		$this->view->request = $params['request'];
		$this->view->dispatchToken = $params['dispatch-token'];
		$this->view->exception = $params['exception'];
		
		$this->response->setResponseCode(500);
	}
}