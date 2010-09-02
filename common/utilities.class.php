<?php
/**
 * $Id: utilities.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common
 */

/**
 * This class provides some common methods that are used throughout the
 * platform.
 *
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class Utilities
{
    const TOOLBAR_DISPLAY_ICON = 1;
    const TOOLBAR_DISPLAY_LABEL = 2;
    const TOOLBAR_DISPLAY_ICON_AND_LABEL = 3;

    private static $us_camel_map = array();
    private static $us_camel_map_with_spaces = array();
    private static $camel_us_map = array();
    private static $camel_us_map_with_spaces = array();

    /**
     * Splits a Google-style search query. For example, the query
     * /"chamilo repository" utilities/ would be parsed into
     * array('chamilo repository', 'utilities').
     * @param $pattern The query.
     * @return array The query's parts.
     */
    static function split_query($pattern)
    {
        $matches = array();
        preg_match_all('/(?:"([^"]+)"|""|(\S+))/', $pattern, $matches);
        $parts = array();
        for($i = 1; $i <= 2; $i ++)
        {
            foreach ($matches[$i] as $m)
            {
                if (! is_null($m) && strlen($m) > 0)
                    $parts[] = $m;
            }
        }
        return (count($parts) ? $parts : null);
    }

    /**
     * Transforms a search string (given by an end user in a search form) to a
     * Condition, which can be used to retrieve learning objects from the
     * repository.
     * @param string $query The query as given by the end user.
     * @param mixed $properties The learning object properties which should be
     * taken into account for the condition. For
     * example, array('title','type') will yield a
     * Condition which can be used to search for
     * learning objects on the properties 'title' or
     * 'type'. By default the properties are 'title'
     * and 'description'. If the condition should
     * apply to a single property, you can pass a
     * string instead of an array.
     * @return Condition The condition.
     */
    static function query_to_condition($query, $properties = array (ContentObject :: PROPERTY_TITLE, ContentObject :: PROPERTY_DESCRIPTION))
    {
        if (! is_array($properties))
        {
            $properties = array($properties);
        }
        $queries = self :: split_query($query);
        if (is_null($queries))
        {
            return null;
        }
        $cond = array();
        foreach ($queries as $q)
        {
            $q = '*' . $q . '*';
            $pattern_conditions = array();
            foreach ($properties as $index => $property)
            {
                $pattern_conditions[] = new PatternMatchCondition($property, $q);
            }
            if (count($pattern_conditions) > 1)
            {
                $cond[] = new OrCondition($pattern_conditions);
            }
            else
            {
                $cond[] = $pattern_conditions[0];
            }
        }
        $result = new AndCondition($cond);
        return $result;
    }

    /**
     * Converts a date/time value retrieved from a FormValidator datepicker
     * element to the corresponding UNIX itmestamp.
     * @param string $string The date/time value.
     * @return int The UNIX timestamp.
     */
    static function time_from_datepicker($string)
    {
        list($date, $time) = split(' ', $string);
        list($year, $month, $day) = split('-', $date);
        list($hours, $minutes, $seconds) = split(':', $time);
        return mktime($hours, $minutes, $seconds, $month, $day, $year);
    }

    /**
     * Converts a date/time value retrieved from a FormValidator datepicker without timepicker
     * element to the corresponding UNIX itmestamp.
     * @param string $string The date/time value.
     * @return int The UNIX timestamp.
     */
    static function time_from_datepicker_without_timepicker($string, $h = 0, $m = 0, $s = 0)
    {
        list($year, $month, $day) = split('-', $string);
        return mktime($h, $m, $s, $month, $day, $year);
    }

    /**
     * Orders the given learning objects by their title. Note that the
     * ordering happens in-place; there is no return value.
     * @param array $objects The learning objects to order.
     */
    static function order_content_objects_by_title($objects)
    {
        usort($objects, array(get_class(), 'by_title'));
    }

    static function order_content_objects_by_id_desc($objects)
    {
        usort($objects, array(get_class(), 'by_id_desc'));
    }

    /**
     * Prepares the given learning objects for use as a value for the
     * element_finder QuickForm element.
     * @param array $objects The learning objects.
     * @return array The value.
     */
    static function content_objects_for_element_finder($objects)
    {
        $return = array();
        foreach ($objects as $object)
        {
            $id = $object->get_id();
            $return[$id] = self :: content_object_for_element_finder($object);
        }
        return $return;
    }

    /**
     * Prepares the given learning object for use as a value for the
     * element_finder QuickForm element's value array.
     * @param ContentObject $object The learning object.
     * @return array The value.
     */
    static function content_object_for_element_finder($object)
    {
        $type = $object->get_type();
        // TODO: i18n
        $date = date('r', $object->get_modification_date());
        $return = array();
        $return['id'] = 'lo_' . $object->get_id();
        $return['classes'] = 'type type_' . $type;
        $return['title'] = $object->get_title();
        $return['description'] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName') . ' (' . $date . ')';
        return $return;
    }

    /**
     * Converts the given under_score string to CamelCase notation.
     * @param string $string The string in under_score notation.
     * @return string The string in CamelCase notation.
     */
    static function underscores_to_camelcase($string)
    {
        if (! isset(self :: $us_camel_map[$string]))
        {
            self :: $us_camel_map[$string] = ucfirst(preg_replace('/_([a-z])/e', 'strtoupper("\1")', $string));
        }
        return self :: $us_camel_map[$string];
    }

    static function underscores_to_camelcase_with_spaces($string)
    {
        if (! isset(self :: $us_camel_map_with_spaces[$string]))
        {
            self :: $us_camel_map_with_spaces[$string] = ucfirst(preg_replace('/_([a-z])/e', '" " . strtoupper("\1")', $string));
        }
        return self :: $us_camel_map_with_spaces[$string];
    }

    /**
     * Converts the given CamelCase string to under_score notation.
     * @param string $string The string in CamelCase notation.
     * @return string The string in under_score notation.
     */
    static function camelcase_to_underscores($string)
    {
        if (! isset(self :: $camel_us_map[$string]))
        {
            self :: $camel_us_map[$string] = preg_replace(array('/^([A-Z])/e', '/([A-Z])/e'), array('strtolower("\1")', '"_".strtolower("\1")'), $string);
        }
        return self :: $camel_us_map[$string];
    }

    /**
     * Compares learning objects by title.
     * @param ContentObject $content_object_1
     * @param ContentObject $content_object_2
     * @return int
     */
    static function by_title($content_object_1, $content_object_2)
    {
        return strcasecmp($content_object_1->get_title(), $content_object_2->get_title());
    }

    private static function by_id_desc($content_object_1, $content_object_2)
    {
        return ($content_object_1->get_id() < $content_object_2->get_id() ? 1 : - 1);
    }

    /**
     * Checks if a file is an HTML document.
     */
    // TODO: SCARA - MOVED / FROM: document_form_class / TO: Utilities or some other relevant class.
    static function is_html_document($path)
    {
        return (preg_match('/\.x?html?$/', $path) === 1);
    }

    static function build_uses($publication_attr)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        $html = array();
        $html[] = '<ul class="publications_list">';
        foreach ($publication_attr as $info)
        {
            $publisher = $udm->retrieve_user($info->get_publisher_user_id());
            $object = $rdm->retrieve_content_object($info->get_publication_object_id());
            $html[] = '<li>';
            // TODO: i18n
            // TODO: SCARA - Find cleaner solution to display Learning Object title + url
            if ($info->get_url())
            {
                $html[] = '<a href="' . $info->get_url() . '">' . $info->get_application() . ': ' . $info->get_location() . '</a>';
            }
            else
            {
                $html[] = $info->get_application() . ': ' . $info->get_location();
            }
            $html[] = ' > <a href="' . $object->get_view_url() . '">' . $object->get_title() . '</a> (' . $publisher->get_firstname() . ' ' . $publisher->get_lastname() . ', ' . date('r', $info->get_publication_date()) . ')';
            $html[] = '</li>';
        }
        $html[] = '</ul>';

        return implode($html);
    }

    static function add_block_hider()
    {
        $html = array();

        $html[] = '<script type="text/javascript">';
        $html[] .= 'function showElement(item)';
        $html[] .= '{';
        $html[] .= '	if (document.getElementById(item).style.display == \'block\')';
        $html[] .= '  {';
        $html[] .= '		document.getElementById(item).style.display = \'none\';';
        $html[] .= '		document.getElementById(\'plus-\'+item).style.display = \'inline\';';
        $html[] .= '		document.getElementById(\'minus-\'+item).style.display = \'none\';';
        $html[] .= '  }';
        $html[] .= '	else';
        $html[] .= '  {';
        $html[] .= '		document.getElementById(item).style.display = \'block\';';
        $html[] .= '		document.getElementById(\'plus-\'+item).style.display = \'none\';';
        $html[] .= '		document.getElementById(\'minus-\'+item).style.display = \'inline\';';
        $html[] .= '		document.getElementById(item).value = \'Version comments here ...\';';
        $html[] .= '	}';
        $html[] .= '}';
        $html[] .= '</script>';

        return implode("\n", $html);
    }

    static function build_block_hider($id = null, $message = null, $display_block = false)
    {
        $html = array();

        if (isset($id))
        {
            if (! isset($message))
            {
                $message = self :: underscores_to_camelcase($id);
            }

            $show_message = 'Show' . $message;
            $hide_message = 'Hide' . $message;

            $html[] = '<div id="plus-' . $id . '"><a href="javascript:showElement(\'' . $id . '\')">' . Translation :: get('Show' . $message) . '</a></div>';
            $html[] = '<div id="minus-' . $id . '" style="display: none;"><a href="javascript:showElement(\'' . $id . '\')">' . Translation :: get('Hide' . $message) . '</a></div>';
            $html[] = '<div id="' . $id . '" style="display: ' . ($display_block ? 'block' : 'none') . ';">';
        }
        else
        {
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    // 2 simple functions to display an array, a bit prettier as print_r
    // for testing purposes only!
    // @author Dieter De Neef
    static function DisplayArray($array)
    {
        $depth = 0;
        if (is_array($array))
        {
            echo "Array (<br />";
            for($i = 0; $i < count($array); $i ++)
            {
                if (is_array($array[$i]))
                {
                    DisplayInlineArray($array[$i], $depth + 1, $i);
                }
                else
                {
                    echo "[" . $i . "] => " . $array[$i];
                    echo "<br />";
                    $depth = 0;
                }
            }
            echo ")<br />";
        }
        else
        {
            echo "Variabele is geen array";
        }
    }

    static function DisplayInlineArray($inlinearray, $depth, $element)
    {
        $spaces = null;
        for($j = 0; $j < $depth - 1; $j ++)
        {
            $spaces .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        echo $spaces . "[" . $element . "]" . "Array (<br />";
        $spaces .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        for($i = 0; $i < count($inlinearray); $i ++)
        {
            $key = key($inlinearray);
            if (is_array($inlinearray[$i]))
            {
                DisplayInlineArray($inlinearray[$i], $depth + 1, $i);
            }
            else
            {
                echo $spaces . "[" . $key . "] => " . $inlinearray[$key];
                echo "<br />";
            }
            next($inlinearray);
        }
        echo $spaces . ")<br />";
    }

    static function format_seconds_to_hours($seconds)
    {
        $hours = floor($seconds / 3600);
        $rest = $seconds % 3600;

        $minutes = floor($rest / 60);
        $seconds = $rest % 60;

        if ($minutes < 10)
        {
            $minutes = '0' . $minutes;
        }

        if ($seconds < 10)
        {
            $seconds = '0' . $seconds;
        }

        return $hours . ':' . $minutes . ':' . $seconds;
    }

    static function format_seconds_to_minutes($seconds)
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        if ($minutes < 10)
        {
            $minutes = '0' . $minutes;
        }

        if ($seconds < 10)
        {
            $seconds = '0' . $seconds;
        }

        return $minutes . ':' . $seconds;
    }

    /**
     * Strips the tags on request, and truncates if necessary a given string to the given length in characters.
     * Adds a character at the end (either specified or default ...) when the string is truncated.
     * Boolean $strip to indicate if the tags within the string have to be stripped
     * @param string $string  The input string, UTF-8 encoded.
     * @param int $length     The limit of the resulting length in characters.
     * @param boolean $strip  Indicates if the tags within the string have to be stripped.
     * @param string $char    A UTF-8 encoded character put at the end of the result string indicating truncation,
     * by default it is the horizontal ellipsis (\u2026)
     * @return string         The result string, html-entities (if any) are converted to normal UTF-8 characters.
     */
    static function truncate_string($string, $length = 200, $strip = true, $char = "\xE2\x80\xA6")
    {
        if ($strip)
        {
            $string = strip_tags($string);
        }

        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        if (mb_strlen($string, 'UTF-8') > $length)
        {
            $string = mb_substr($string, 0, $length - mb_strlen($char, 'UTF-8'), 'UTF-8') . $char;
        }

        return $string;
    }

    static function extract_xml_file($file, $extra_options = array())
    {
        require_once 'XML/Unserializer.php';
        if (file_exists($file))
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);

            foreach ($extra_options as $op => $value)
                $unserializer->setOption($op, $value);

            // userialize the document
            $status = $unserializer->unserialize($file, true);
            if (PEAR :: isError($status))
            {
                return false;
            }
            else
            {
                $data = $unserializer->getUnserializedData();
                return $data;
            }
        }
        else
        {
            return null;
        }
    }

    static function set_application($application)
    {
        Translation :: set_application($application);
        Theme :: set_application($application);
    }

    static function display_true_false_icon($value)
    {
        if ($value)
        {
            $icon = 'action_setting_true.png';
        }
        else
        {
            $icon = 'action_setting_false.png';
        }
        return '<img src="' . Theme :: get_common_image_path() . $icon . '">';
    }

    static function htmlentities($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
    }

    static function get_usable_memory()
    {
        $val = trim(@ini_get('memory_limit'));

        if (preg_match('/(\\d+)([mkg]?)/i', $val, $regs))
        {
            $memory_limit = (int) $regs[1];
            switch ($regs[2])
            {

                case 'k' :
                case 'K' :
                    $memory_limit *= 1024;
                    break;

                case 'm' :
                case 'M' :
                    $memory_limit *= 1048576;
                    break;

                case 'g' :
                case 'G' :
                    $memory_limit *= 1073741824;
                    break;
            }

            // how much memory PHP requires at the start of export (it is really a little less)
            if ($memory_limit > 6100000)
            {
                $memory_limit -= 6100000;
            }

            // allow us to consume half of the total memory available
            $memory_limit /= 2;
        }
        else
        {
            // set the buffer to 1M if we have no clue how much memory PHP will give us :P
            $memory_limit = 1048576;
        }

        return $memory_limit;
    }

    static function mimetype_to_image($mimetype)
    {
        $mimetype_image = str_replace('/', '_', $mimetype);
        return Theme :: get_common_image('mimetype/' . $mimetype_image, 'png', $mimetype, '', ToolbarItem :: DISPLAY_ICON);
    }
}
?>