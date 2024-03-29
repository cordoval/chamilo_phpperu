<?php
namespace common\libraries;

use DOMDocument;

/*
==============================================================================
	Chamilo - elearning and course management software

	Copyright (c) 2004 Chamilo S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors
	Copyright (c) 2008 Hans De Bisschop

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Chamilo, 181 rue Royale, B-1000 Brussels, Belgium, info@chamilo.org
==============================================================================
*/
/**
==============================================================================
 *	This is the text library for Chamilo.
 *	Include/require it in your code to use its functionality.
 *
 *	@package common.html
==============================================================================
 */

class Text
{

    /**
     * function make_clickable($string)
     *
     * @desc   completes url contained in the text with "<a href ...".
     *         However the function simply returns the submitted text without any
     *         transformation if it already contains some "<a href:" or "<img src=".
     * @param string $text text to be converted
     * @return text after conversion
     * @author Rewritten by Nathan Codding - Feb 6, 2001.
     *         completed by Hugues Peeters - July 22, 2002
     *
     * Actually this function is taken from the PHP BB 1.4 script
     * - Goes through the given string, and replaces xxxx://yyyy with an HTML <a> tag linking
     * 	to that URL
     * - Goes through the given string, and replaces www.xxxx.yyyy[zzzz] with an HTML <a> tag linking
     * 	to http://www.xxxx.yyyy[/zzzz]
     * - Goes through the given string, and replaces xxxx@yyyy with an HTML mailto: tag linking
     *		to that email address
     * - Only matches these 2 patterns either after a space, or at the beginning of a line
     *
     * Notes: the email one might get annoying - it's easy to make it more restrictive, though.. maybe
     * have it require something like xxxx@yyyy.zzzz or such. We'll see.
     */
    public static function make_clickable($string)
    {
        if (! stristr($string, ' src=') && ! stristr($string, ' href='))
        {
            $string = eregi_replace("(https?|ftp)://([a-z0-9#?/&=._+:~%-]+)", "<a href=\"\\1://\\2\" target=\"_blank\">\\1://\\2</a>", $string);
            $string = eregi_replace("([a-z0-9_.-]+@[a-z0-9.-]+)", "<a href=\"mailto:\\1\">\\1</a>", $string);
        }

        return $string;
    }
    
	/**
	 *    Get the ordinal suffix of an int (e.g. th, rd, st, etc.)
	 *     @param	int	$n
	 *     @return	string	$n + $n's ordinal suffix
	 */
	function ordinal_suffix($n) {
	     $n_last = $n % 100;
	     if (($n_last > 10 && $n_last < 14) || $n == 0){
	          return "{$n}th";
	     }
	     switch(substr($n, -1)) {
	          case '1':    return "{$n}st";
	          case '2':    return "{$n}nd";
	          case '3':    return "{$n}rd";
	          default:     return "{$n}th";
	     }
	}

    /**
     * Apply parsing to content to parse tex commandos that are seperated by [tex]
     * [/tex] to make it readable for techexplorer plugin.
     * @param string $text The text to parse
     * @return string The text after parsing.
     * @author Patrick Cool <patrick.cool@UGent.be>
     * @version June 2004
     */
    public static function parse_tex($textext)
    {
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
        {
            $textext = str_replace(array("[tex]", "[/tex]"), array("<object classid=\"clsid:5AFAB315-AD87-11D3-98BB-002035EFB1A4\"><param name=\"autosize\" value=\"true\" /><param name=\"DataType\" value=\"0\" /><param name=\"Data\" value=\"", "\" /></object>"), $textext);
        }
        else
        {
            $textext = str_replace(array("[tex]", "[/tex]"), array("<embed type=\"application/x-techexplorer\" texdata=\"", "\" autosize=\"true\" pluginspage=\"http://www.integretechpub.com/techexplorer/\">"), $textext);
        }
        return $textext;
    }

    public static function generate_password($length = 8)
    {
        $characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        if ($length < 2)
        {
            $length = 2;
        }
        $password = '';
        for($i = 0; $i < $length; $i ++)
        {
            $password .= $characters[rand() % strlen($characters)];
        }
        return $password;
    }

    public static function parse_query_string($query = '')
    {
        $queries = array();
        $variables = explode('&', $query);

        foreach ($variables as $variable)
        {
            list($key, $value) = explode('=', $variable, 2);
            $queries[$key] = $value;
        }

        return $queries;
    }

    // TODO: There have to be a better alternatives for this ...
    public static function strip_text($text)
    {
        $i = - 1;
        $n = '';
        $ok = 1;

        while (isset($text{++ $i}))
        {
            if ($ok && $text{$i} != '<')
            {
                continue;
            }
            elseif ($text{$i} == '>')
            {
                $ok = 1;
                $n .= '>';
                continue;
            }
            elseif ($text{$i} == '<')
            {
                $ok = 0;
            }

            if (! $ok)
            {
                $n .= $text{$i};
            }
        }

        return $n;
    }

    // TODO: There have to be a better alternatives for this ...
    public static function fetch_tag_into_array($source, $tag = "<img>")
    {
        $data = self :: strip_text($source);
        $data = ">" . $data;
        $striped_data = strip_tags($data, $tag);

        $my_array = explode("><", $striped_data);

        foreach ($my_array as $main_key => $main_value)
        {
            $my_space_array[$main_key] = explode(" ", $main_value);
            foreach ($my_space_array[$main_key] as $sub_key => $sub_value)
            {
                $my_pre_fetched_tag_array = explode("=", $sub_value);
                // check for null attributes ...
                if (($my_pre_fetched_tag_array[1] != '""') && ($my_pre_fetched_tag_array[1] != NULL))
                {
                    $my_tag_array[$main_key][$my_pre_fetched_tag_array[0]] = substr($my_pre_fetched_tag_array[1], 1, - 1);
                }
            }
        }

        return $my_tag_array;
    }

    public static function parse_html_file($string, $tag = 'img')
    {
    	$document = new DOMDocument();
        $document->loadHTML($string);
        return $document->getElementsByTagname($tag);
    }

    public static function highlight($haystack, $needle, $highlight_color)
    {
        if (strlen($highlight_color) < 1 || strlen($haystack) < 1 || strlen($needle) < 1)
        {
            return $haystack;
        }

        $matches = array();
        $matches_done = array();

        preg_match_all("/$needle+/i", $haystack, $matches);

        if (is_array($matches[0]) && count($matches[0]) >= 1)
        {
            foreach ($matches[0] as $match)
            {
                if (in_array($match, $matches_done))
                    continue;

                $matches_done[] = $match;
                $haystack = str_replace($match, '<span style="background-color:' . $highlight_color . ';">' . $match . '</span>', $haystack);
            }
        }
        return $haystack;
    }

    /*	Convert strings from one character set to another
	 * 	Can avoid weird characters in output for non default alphanumeric symbols
	 *
	 * 	Example
	 *  $string = htmlentities($string, ENT_COMPAT, 'cp1252');
	 *	$string = iconv('windows-1252', 'ISO-8859-1//TRANSLIT', $string);
	 */
    public function convert_character_set($string, $from, $to)
    {
        $string = htmlentities($string, ENT_COMPAT, $from);
        $string = iconv($from, $to . '//TRANSLIT', $string);

        return $string;
    }

    public function create_link($url, $text, $new_page = false, $class = null, $styles = array())
    {
        $link = '<a href="' . $url . '" ';

        if ($new_page)
            $link .= 'target="about:blank" ';

        if ($class)
            $link .= 'class="' . $class . '" ';

        if (count($styles) > 0)
        {
            $link .= 'style="';

            foreach ($styles as $name => $value)
            {
                $link .= $name . ': ' . $value . ';';
            }

            $link .= '" ';
        }

        $link .= '>' . $text . '</a>';

        return $link;
    }

    /**
     * Function to recreate the charAt function from javascript
     * Found at http://be.php.net/manual/en/function.substr.php#81491
     *
     * @param String $str
     * @param Position $pos
     * @return Char or -1
     */
    function char_at($str, $pos)
    {
        return (substr($str, $pos, 1) !== false) ? substr($str, $pos, 1) : - 1;
    }

    public static function remove_non_alphanumerical($string)
    {
        $string = str_replace (' ', '', $string);
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
        return Utilities :: camelcase_to_underscores($string);
    }
}
?>