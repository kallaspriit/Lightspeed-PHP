<?php

/**
 * Static utility methods class.
 */
class Util {
	
	public static function dbDateToDate($dbDate) {
		return substr($dbDate, 8, 2).'.'.substr($dbDate, 5, 2).'.'.substr($dbDate, 0, 4);
	}
	
	/**
	 * Truncates given string to some length using given delimiter
	 *
	 * @param string $string String to truncate
	 * @param int $limit How many characters to allow
	 * @param string $break Break character, use space to truncate to words
	 * @param string $pagger What to append to truncated string
	 * @return string Truncated string
	 */
	public static function truncate($string, $limit = 300, $break = '.', $padder = '...'){
		$length = mb_strlen($string);
        $padderLength = mb_strlen($padder);

		if($length <= $limit) {
			return $string;
		}

		$breakpoint = mb_strrpos(substr($string, 0, $limit), $break, $limit - ($limit / 4));

		if ($breakpoint === false) {
			$breakpoint = mb_strrpos($string, ' ', -($length - $limit + $padderLength));
		}

		if ($breakpoint !== false && $breakpoint < $length - 1) {
			$string = mb_substr($string, 0, $breakpoint) . $padder;
		}

		return $string;
	}

}