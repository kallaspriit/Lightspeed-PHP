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
 * @subpackage Router
 */

// Require the lightspeed base validator implementation
require_once LIBRARY_PATH.'/validator/ValidatorBase.php';

/**
 * Validator for user-entered data.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Application
 * @subpackage Validator
 */
class Validator extends ValidatorBase {

	/**
	 * You can override any of the ValidatorBase methods or add new ones
	 * as required
	 */

	/**
	 * Checks whether given personal code matches the rules of the estonian
	 * social security number specification.
	 * 
	 * @param string $code The code to check
	 * @param string $inputId The source input id
	 * @return boolean Is the personal code valid 
	 */
	public function personalCode($code, $inputId) {
		$invalid = false;
		
		if (strlen($code) != 11) {
			$invalid = true;
		}
		
		$firstNumber = (int)substr($code, 0, 1);
		
		if ($firstNumber < 1 || $firstNumber > 6) {
			$invalid = true;
		}
		
		$birthYear = (int)substr($code, 1, 2);
		$birthMonth = (int)substr($code, 3, 2);
		$birthDay = (int)substr($code, 5, 2);
		
		if (!checkdate($birthMonth, $birthDay, $birthYear)) {
			$invalid = true;
		}
		
		$orderNumber = (int)substr($code, 7, 3);
		
		if ($orderNumber < 1 || $orderNumber > 659) {
			$invalid = true;
		}
		
		$checkNumber = (int)substr($code, 10, 1);
		
		$expectedCheckNumber = 0;

		for ($i = 1; $i < 11; $i++) {
			$expectedCheckNumber += ($i % 10 + intval($i / 10)) * substr($code, $i - 1, 1);
		}

		$expectedCheckNumber = $expectedCheckNumber % 11;

		if ($expectedCheckNumber == 10) {
			$expectedCheckNumber = 0;

			for ($i = 3; $i < 13; $i++) {
				$expectedCheckNumber += ($i % 10 + intval($i / 10)) * substr($code, $i - 3, 1);
			}

			$expectedCheckNumber = $expectedCheckNumber % 11;

			if ($expectedCheckNumber == 10) {
				$expectedCheckNumber = 0;
			}
		}

		if ($checkNumber != $expectedCheckNumber) {
			$invalid = true;
		}
		
		if ($invalid) {
			$this->addError(
				$inputId,
				'validator.error.invalid-personal-code'
			);

			return false;
		} else {
			return true;
		}
	}
}