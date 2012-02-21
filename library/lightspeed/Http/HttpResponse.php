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
 * @subpackage Http
 */

/**
 * Represents a HTTP response to a request
 *
 * TODO: Add funtionality for headers and status codes
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Http
 */
class HttpResponse {

	/**
	 * The content to send back
	 * 
	 * @var string
	 */
	protected $content = '';
	
	/**
	 * Http response code to send.
	 * 
	 * Will only be sent if changed from default.
	 * 
	 * @var integer 
	 */
	protected $responseCode;
	
	/**
	 * Array of http response code messages.
	 * 
	 * Stolen from Zend Framework.
	 * 
	 * @var array
	 */
	protected static $httpCodeMessages = array(
		
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

	/**
	 * Sets the output content to send back.
	 *
	 * @param string $content Application output
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * Appends content to the output to send back.
	 *
	 * @param string $content Content to append to current
	 */
	public function append($content) {
		$this->content .= $content;
	}

	/**
	 * Prepends content to the output to send back.
	 *
	 * @param string $content Content to append to current
	 */
	public function prepend($content) {
		$this->content = $content.$this->content;
	}
	
	/**
	 * Sets the http response code to send.
	 * 
	 * @param integer $httpCode The http code to use
	 * @throws Exception If unknown http code provided
	 */
	public function setResponseCode($httpCode) {
		if (!array_key_exists($httpCode, self::$httpCodeMessages)) {
			throw new Exception('Unknown http response code "'.$httpCode.'"');
		}
		
		$this->responseCode = $httpCode;
	}

	/**
	 * Clears currently accumulated content back to an empty string.
	 */
	public function clear() {
		$this->content = '';
	}

	/**
	 * Returns response content.
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * Sends the response to standard output
	 */
	public function send() {
		if ($this->responseCode !== null) {
			header(
				'HTTP/1.0 '.$this->responseCode.' '.
				self::$httpCodeMessages[$this->responseCode]
			);
		}
		
		echo $this->content;
	}
}