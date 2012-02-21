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
 * @subpackage Controller
 */

// Require the lightspeed base controller implementation
require_once LIGHTSPEED_PATH.'/Controller/ControllerBase.php';

// Require the application implementation of the view
require_once APPLICATION_PATH.'/View.php';

/**
 * Application action controller base implementation.
 *
 * Extends the basic lightspeed implementation of ControllerBase and adds
 * functionality of layout and views.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Application
 * @subpackage Controller
 */
class Controller extends ControllerBase {

	/**
	 * The layout to render around the view.
	 *
	 * @var View
	 */
	protected $layout;

	/**
	 * The view to render as an action of actions.
	 *
	 * @var View
	 */
	protected $view;

	/**
	 * Constructs the controller.
	 *
	 * Initiates application implementations of the view.
	 */
	public function  __construct() {
		$this->layout = new View();
		$this->view = new View();
	}
	
	/**
	 * You can override any of the ControllerBase methods if you wish the
	 * controller to work differently then the default implementation, you may
	 * also of-course add new methods to suit your needs.
	 */

}