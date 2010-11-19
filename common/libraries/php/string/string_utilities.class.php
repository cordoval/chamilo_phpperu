<?php
namespace common\libraries;

use Exception;
/**
 * $Id: string.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.string
 */
class StringUtilities
{

    /**
     * Tests if a string starts with a given string
     *
     * @param string $string
     * @param string $start
     * @return bool
     */
    public static function start_with($string, $start, $case_sensitive = true)
    {
        if($case_sensitive)
        {
            return strpos($string, $start) === 0;
        }
        else
        {
            return stripos($string, $start) === 0;
        }
    }

    /**
     * Tests if a string ends with the given string
     *
     * @param string $string
     * @param string $end
     * @param bool $case_sensitive
     * @return bool
     */
    public static function end_with($string, $end, $case_sensitive = true)
    {
        if($case_sensitive)
        {
            return strrpos($string, $end) === strlen($string) - strlen($end);
        }
        else
        {
            return strripos($string, $end) === strlen($string) - strlen($end);
        }
    }

    /**
     * Indicates wether the given string has some value (meaning neither null nor empty)
     * @param string $string
     * @return boolean
     */
    public static function has_value($string)
    {
        return !StringUtilities :: is_null_or_empty($string);
    }

    /**
     * Indicates wether the given string is null or empty
     * @param string $string
     * @return boolean
     */
    public static function is_null_or_empty($string)
    {
        if(isset($string))
        {
            if(is_string($string))
            {
                if(strlen($string) == 0)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                throw new Exception('StringUtilities error: The given value is not a string');
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * Ensure a string starts with another given string
     *
     * @param $string The string that must start with a leading string
     * @param $leading_string The string to add at the beginning of the main string if necessary
     * @return string
     */
    public static function ensure_start_with($string, $leading_string)
    {
        if (StringUtilities :: start_with($string, $leading_string))
        {
            return $string;
        }
        else
        {
            return $leading_string . $string;
        }
    }

    /**
     * Remove a trailing string from a string if it exists
     * @param $string The string that must be shortened if it ends with a trailing string
     * @param $trailing_string The trailing string
     * @return string
     */
    public static function remove_trailing($string, $trailing_string)
    {
        if (StringUtilities :: end_with($string, $trailing_string))
        {
            return substr($string, 0, strlen($string) - strlen($trailing_string));
        }
        else
        {
            return $string;
        }
    }

    /**
     * Return the string found between two characters.
     *
     * If an index is given, it returns the value at the index position.
     * e.g. $index = 3 --> returns the value between the third occurence of $opening_char and $closing_char
     *
     * @param string $opening_char
     * @param string $closing_char
     * @param int $index 0 based index
     * @return string or null
     */
    public static function get_value_between_chars($haystack, $index = 0, $opening_char = '[', $closing_char = ']')
    {
        $offset = 0;
        $found = true;
        $value = null;

        for($i = 0; $i < $index + 1; $i ++)
        {
            $op_pos = strpos($haystack, $opening_char, $offset);
            if ($op_pos !== false)
            {
                $cl_pos = strpos($haystack, $closing_char, $op_pos + 1);

                if ($cl_pos !== false)
                {
                    $value = substr($haystack, $op_pos + 1, $cl_pos - $op_pos - 1);
                    $offset = $cl_pos + 1;
                }
                else
                {
                    $found = false;
                    break;
                }
            }
            else
            {
                $found = false;
                break;
            }
        }

        if ($found)
        {
            return $value;
        }
        else
        {
            return null;
        }
    }

    /**
     * Build an array from a list of strings
     *
     * E.g: Array with the following strings:
     *
     * 		'general_description[0][0][string]'
     * 		'general_description[0][1][string]'
     * 		'general_description[1][0][string]'
     *
     * @param array $strings Array of (strings => value) pairs to merge into a multilevel array
     * @param string $opening_char
     * @param string $closing_char
     */
    public static function to_multilevel_array($strings, $opening_char = '[', $closing_char = ']')
    {

        $array = array();

        foreach ($strings as $string => $value)
        {
            StringUtilities :: set_next_level_array($array, $string, $value, $opening_char, $closing_char);
        }

        return $array;
    }

    private static function set_next_level_array(&$container_array, $string, $value, $opening_char = '[', $closing_char = ']')
    {
        $key = StringUtilities :: get_value_between_chars($string, 0, $opening_char, $closing_char);
        $sub_string = substr($string, strpos($string, $closing_char) + 1);

        if (isset($sub_string) && strlen($sub_string) > 0)
        {
            if (isset($container_array[$key]))
            {
                $sub_array = $container_array[$key];
            }
            else
            {
                $sub_array = array();
            }

            StringUtilities :: set_next_level_array($sub_array, $sub_string, $value, $opening_char, $closing_char);

            $container_array[$key] = $sub_array;
        }
        else
        {
            if (isset($container_array[$key]))
            {
                $container_array[$key] = array_merge($container_array[$key], $value);
            }
            else
            {
                $container_array[$key] = $value;
            }
        }
    }

    /**
     * Escape a string according to the mysql way of escaping.
     *
     * This function should only be used when it is not possible to use the mysql_real_escape_string()
     * because you don't have any open connection to the database yet
     *
     * @param string $string_to_escape
     * @return string
     */
    public static function escape_mysql($string_to_escape, $quote_char = "'")
    {
        if($quote_char != '"' && $quote_char != "'")
        {
            throw new Exception('Unvalid quote char for MySQL query');
        }

        $string_to_escape = str_ireplace("\\", "\\\\", $string_to_escape);

        if($quote_char == "'")
        {
            $string_to_escape = str_ireplace("'", "\'", $string_to_escape);
        }
        elseif($quote_char == '"')
        {
            $string_to_escape = str_ireplace('"', '\"', $string_to_escape);
        }

        $string_to_escape = str_ireplace("\n", '\n', $string_to_escape);
        $string_to_escape = str_ireplace("\r", '\r', $string_to_escape);
        $string_to_escape = str_ireplace("\x00", '\\\\x00', $string_to_escape);
        $string_to_escape = str_ireplace("\x1a", '\\\\x1a', $string_to_escape);

        return $string_to_escape;
    }

}
?>