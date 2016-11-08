<?php

/**
 * Text helper class. Provides simple methods for working with text.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Text {


	public static function limit_chars($str, $limit = 100,  $preserve_words = FALSE)
	{
		$limit = (int) $limit;

		if (trim($str) === '' OR UTF8::strlen($str) <= $limit)
			return $str;

		if ($limit <= 0)
			return null;

		if ($preserve_words === FALSE)
			return rtrim(UTF8::substr($str, 0, $limit));

		// Don't preserve words. The limit is considered the top limit.
		// No strings with a length longer than $limit should be returned.
		if ( ! preg_match('/^.{0,'.$limit.'}\s/us', $str, $matches))
			return null;

		return rtrim($matches[0]);
	}



}
