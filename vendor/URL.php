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
class URL {


    public static function title($title, $separator = '-', $ascii_only = FALSE)
    {
        if ($ascii_only === TRUE)
        {
            // Transliterate non-ASCII characters
            $title = UTF8::transliterate_to_ascii($title);

            // Remove all characters that are not the separator, a-z, 0-9, or whitespace
            $title = preg_replace('![^'.preg_quote($separator).'a-z0-9\s]+!', '', strtolower($title));
        }
        else
        {
            // Remove all characters that are not the separator, letters, numbers, or whitespace
            $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', UTF8::strtolower($title));
        }

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        // Trim separators from the beginning and end
        return trim($title, $separator);
    }

}
