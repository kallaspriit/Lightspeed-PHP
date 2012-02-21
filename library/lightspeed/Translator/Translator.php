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
 * @subpackage Translator
 */

/**
 * Translator translates simple and printf-syntax strings.
 *
 * @author Priit Kallas <kallaspriit@gmail.com>
 * @package Lightspeed
 * @subpackage Translator
 */
class Translator {

	/**
	 * Array of translations.
	 *
	 * The array keys should be the translation names and the values arrays
	 * containing the translations with language ids as keys.
	 *
	 * @var array
	 */
	protected $translations = array();
	
	/**
	 * Active language id.
	 * 
	 * Translations will be returned for this language.
	 * 
	 * @var integer
	 */
	protected $languageId = 1;

	/**
	 * Name of the instance
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Array of named instances of the class.
	 *
	 * Class implements singleton registry pattern (multiton). The multiton
	 * pattern expands on the singleton concept to manage a map of named
	 * instances as key-value pairs.
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * Protected constructor, use {@see Translator::getInstance()}
	 *
	 * @param string $name Name of the instance
	 */
	protected function  __construct($name) {
		$this->name = $name;
	}

	//@codeCoverageIgnoreStart

	/**
	 * Cloning of this object is not allowed
	 */
	protected function  __clone() {}

	//@codeCoverageIgnoreEnd

	/**
	 * Returns a named instance of the class.
	 *
	 * If the instance does not already exist, it is created.
	 *
	 * @param string $name Name of the instance to fetch,
	 * @return Translator
	 */
	public static function getInstance($name = 'main') {
		if (!isset(self::$instances[$name])) {
			self::$instances[$name] = new self($name);
		}

		return self::$instances[$name];
	}

	/**
	 * Returns name of current translator instance
	 *
	 * @return string Name of the instance
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the active language to use for translating.
	 *
	 * Use one of the LANGUAGE_.. constants, the same you use for defining the
	 * translations in backend/application/translations.php.
	 *
	 * @param integer $languageId The language id
	 */
	public function setLanguage($languageId) {
		$this->languageId = $languageId;
	}

	/**
	 * Returns currently active language id
	 *
	 * @return integer Active language id
	 */
	public function getLanguage() {
		return $this->languageId;
	}

	/**
	 * Sets the array of translations to use.
	 *
	 * This replaces any current translations.
	 *
	 * The array keys should be the translation names and the values arrays
	 * containing the translations with language ids as keys.
	 *
	 * @param array $translations Translations to set
	 */
	public function setTranslations(array $translations) {
		$this->translations = $translations;
	}

	/**
	 * Adds an array of translations to use.
	 *
	 * This adds the translations to existing.
	 *
	 * The array keys should be the translation names and the values arrays
	 * containing the translations with language ids as keys.
	 *
	 * @param array $translations Translations to add
	 */
	public function addTranslations(array $translations) {
		$this->translations = array_merge($this->translations, $translations);
	}

	/**
	 * Adds a single translation.
	 *
	 * The translation should be an array with keys of language ids and values
	 * the translations in given language.
	 *
	 * @param integer $language Language id
	 * @param string $key Key of the translation to add
	 * @param string $translation The actual translation
	 */
	public function addTranslation($language, $key, $translation) {
		$this->translations[$key] = array($language => $translation);
	}

	/**
	 * Returns currently used translations.
	 *
	 * @return array Currently used translations array
	 */
	public function getTranslations() {
		return $this->translations;
	}

	/**
	 * Returns whether a translation exists.
	 *
	 * If no language id is given as second argument, currently active language
	 * is used.
	 *
	 * @param string $key Key to check
	 * @param integer|null $language Language to check
	 * @return boolean Does the translation exist
	 */
	public function translationExists($key, $language = null) {
		if ($language == null) {
			$language = $this->languageId;
		}

		if (substr($key, -5) == ':html') {
			$key = substr($key, 0, -5);
		}

		if (isset($this->translations[$key][$language])) {
			return true;
		}

		return false;
	}

	/**
	 * Translates a translation key in currently active language.
	 *
	 * A translation may contain dynamic placeholders in printf format that
	 * are replaced with data given as any number of extra parameters to this
	 * method.
	 *
	 * For example, consider translation named 'hello' defined as
	 * "Hello %s, you are %d years old". This can be translated using
	 * $translator->translate('hello', 'Priit', 22) and it would be returned
	 * as "Hello Priit, you are 22 years old".
	 *
	 * The extra data to replace can be given either as any number of extra
	 * parameters or as an array of data as the second parameter. For example,
	 * the 'hello' could have been also translated with a call to
	 * $translator->translate('hello', array('Priit', 22)).
	 *
	 * By default, HTML in the result is escaped with htmlspecialchars(). If you
	 * wish to include HTML in the result, append ":html" to the key. For
	 * example $translator->translate('hello:html', '<b>Priit</b>', 22)
	 * would return "Hello <b>Priit</b>, you are 22 years old", without the
	 * :html appended, the <> symbols would be replaced with &lt; and &gt;
	 *
	 * @param string $key The translation key to translate
	 * @param mixed ... Any number of parameters that will be substituted in
	 *					printf format
	 * @return string The translated string
	 * @throws Exception When the translation does not exist
	 */
	public function translate($key /* , param1, param2, .. */) {
		$allowHtml = false;

		if (substr($key, -5) == ':html') {
			$allowHtml = true;

			$key = substr($key, 0, -5);
		}
		
		if (!isset($this->translations[$key][$this->languageId])) {
			if (LS_DEBUG) {
				throw new Exception(
					'Translation for key "'.$key.'" does not exist'
				);
			} else {
				// lets not fail in live because of missing translation
				return $key;
			}
		}
		
		$translation = $this->translations[$key][$this->languageId];

		$arguments = func_get_args();

		if (count($arguments) > 1) {
			if (is_array($arguments[1])) {
				$translation = vsprintf($translation, $arguments[1]);
			} else {
				$translation = vsprintf($translation, array_slice($arguments, 1));
			}
		}

		if ($allowHtml) {
			return $translation;
		} else {
			return htmlspecialchars($translation);
		}
	}

	/**
	 * Translates a translation key in currently active language.
	 *
	 * This is shortcut alias for Translator::getInstance()->translate().
	 *
	 * A translation may contain dynamic placeholders in printf format that
	 * are replaced with data given as any number of extra parameters to this
	 * method.
	 *
	 * For example, consider translation named 'hello' defined as
	 * "Hello %s, you are %d years old". This can be translated using
	 * $translator->translate('hello', 'Priit', 22) and it would be returned
	 * as "Hello Priit, you are 22 years old".
	 *
	 * The extra data to replace can be given either as any number of extra
	 * parameters or as an array of data as the second parameter. For example,
	 * the 'hello' could have been also translated with a call to
	 * $translator->translate('hello', array('Priit', 22)).
	 *
	 * By default, HTML in the result is escaped with htmlspecialchars(). If you
	 * wish to include HTML in the result, append ":html" to the key. For
	 * example $translator->translate('hello:html', '<b>Priit</b>', 22)
	 * would return "Hello <b>Priit</b>, you are 22 years old", without the
	 * :html appended, the <> symbols would be replaced with &lt; and &gt;
	 *
	 * @param string $key The translation key to translate
	 * @param mixed ... Any number of parameters that will be substituted in
	 *					printf format
	 * @return string The translated string
	 * @throws Exception When the translation does not exist
	 */
	public static function get($key /* , param1, param2, .. */) {
		$parameters = func_get_args();
		$instance = self::getInstance();

		return call_user_func_array(array($instance, 'translate'), $parameters);
	}

	/**
	 * Translates a translation key in currently active language and if a
	 * translation for given key does not exist, just returns the key instead of
	 * throwing an exception.
	 *
	 * A translation may contain dynamic placeholders in printf format that
	 * are replaced with data given as any number of extra parameters to this
	 * method.
	 *
	 * For example, consider translation named 'hello' defined as
	 * "Hello %s, you are %d years old". This can be translated using
	 * $translator->translate('hello', 'Priit', 22) and it would be returned
	 * as "Hello Priit, you are 22 years old".
	 *
	 * The extra data to replace can be given either as any number of extra
	 * parameters or as an array of data as the second parameter. For example,
	 * the 'hello' could have been also translated with a call to
	 * $translator->translate('hello', array('Priit', 22)).
	 *
	 * By default, HTML in the result is escaped with htmlspecialchars(). If you
	 * wish to include HTML in the result, append ":html" to the key. For
	 * example $translator->translate('hello:html', '<b>Priit</b>', 22)
	 * would return "Hello <b>Priit</b>, you are 22 years old", without the
	 * :html appended, the <> symbols would be replaced with &lt; and &gt;
	 *
	 * @param string $key The translation key to translate
	 * @param mixed ... Any number of parameters that will be substituted in
	 *					printf format
	 * @return string The translated string
	 * @throws Exception When the translation does not exist
	 */
	public static function getIfExists($key /* , param1, param2, .. */) {
		$parameters = func_get_args();
		$instance = self::getInstance();

		if ($instance->translationExists($key)) {
			return call_user_func_array(
				array($instance, 'translate'),
				$parameters
			);
		}

		return $key;
	}
	
	/**
	 *
	 * @param array $translations
	 * @param type $filename 
	 */
	public static function generateJavascriptFile(array $translations, $filename) {
		$handle = fopen($filename, 'w');
		
		if ($handle === false) {
			throw new Exception(
				'Unable to generate JavaScript translations file, opening "'.
				$filename.'" failed'
			);
		}
		
		$json = json_encode($translations);
		$computerName = getenv('COMPUTERNAME');

		$fileContent = '// Generated '.date('Y-m-d H:i:s').
			(!empty($computerName) ? ' on computer called "'.
			$computerName.'"' : '')."\n".'var _translations = '.$json.';';

		fwrite($handle, $fileContent);
		fclose($handle);
	}

}