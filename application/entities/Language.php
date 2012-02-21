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
 * @subpackage Entities
 */

/**
 * Provides language information.
 */
class Language {

	/**
	 * Returns current language.
	 *
	 * @return integer Language id
	 */
	public static function get() {
		return (int)Session::get('language', LANGUAGE_DEFAULT);
	}
	
	/**
	 * Returns the name abbrevation of current language.
	 * 
	 * @return string 
	 */
	public static function getName() {
		$languageId = self::get();
		
		switch ($languageId) {
			case LANGUAGE_ENGLISH:
				return 'eng';
			
			case LANGUAGE_ESTONIAN:
				return 'est';
				
			default:
				throw new Exception(
					'Unknown current language id: '.$languageId
				);
		}
	}

	/**
	 * Sets active language.
	 *
	 * @param integer $languageId Language identifier
	 */
	public static function set($languageId) {
		Session::set('language', (int)$languageId);
	}
	
	/**
	 * Checks if language is valid.
	 *
	 * @param integer $languageId Language identifier
	 */
	public static function isValid($languageId) {
		if (in_array($languageId, array(LANGUAGE_ENGLISH, LANGUAGE_ESTONIAN))) {
			return $languageId;
		} else {
			return self::get();
		}
	}
}