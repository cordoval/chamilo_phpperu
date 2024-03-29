<?php
namespace common\libraries;

use user\UserDataManager;

use repository\RepositoryDataManager;
use repository\ContentObject;

use XML_Unserializer;
use PEAR;
use Exception;

/**
 * @package common
 *
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

    const COMMON_LIBRARIES = __NAMESPACE__;

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
    static function query_to_condition($query, $properties = array(ContentObject :: PROPERTY_TITLE, ContentObject :: PROPERTY_DESCRIPTION))
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
        $return['description'] = Translation :: get('TypeName', array(), ContentObject :: get_content_object_type_namespace($type)) . ' (' . $date . ')';
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
            self :: $camel_us_map[$string] = preg_replace(array('/^([A-Z])/e', '/([A-Z])/e'), array(
                    'strtolower("\1")',
                    '"_".strtolower("\1")'), $string);
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

    /**
     * @param string $application
     */
    static function set_application($application)
    {
        Translation :: set_application($application);
        Theme :: set_application($application);
    }

    /**
     * @param mixed $value
     * @return string
     */
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

    /**
     * @param string $string
     */
    static function htmlentities($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
    }

    /**
     * @return int
     */
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

    /**
     * @param string $mimetype
     * @return string The image html
     */
    static function mimetype_to_image($mimetype)
    {
        $mimetype_image = str_replace('/', '_', $mimetype);
        return Theme :: get_common_image('mimetype/' . $mimetype_image, 'png', $mimetype, '', ToolbarItem :: DISPLAY_ICON);
    }

    /**
     * @param string $classname
     * @return boolean
     */
    static function autoload($classname)
    {
        $classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            // Non-namespaced class, should be a plugin
            return self :: autoload_plugin($classname);
        }

        $unqualified_class_name = $classname_parts[count($classname_parts) - 1];
        array_pop($classname_parts);
        $autoloader_path = Path :: get(SYS_PATH) . implode('/', $classname_parts) . '/' . 'php/autoloader.class.php';

        if (file_exists($autoloader_path))
        {
            require_once $autoloader_path;
            $autoloader_class = implode('\\', $classname_parts) . '\\Autoloader';
            if ($autoloader_class :: load($unqualified_class_name))
            {
                return true;
            }
        }
        //standard fall back
        $class_filename = self :: camelcase_to_underscores($unqualified_class_name) . '.class.php';
        $class_path = dirname($autoloader_path) . '/' . $class_filename;
        if (file_exists($class_path))
        {
            require_once $class_path;
            return class_exists($classname);
        }
    }

    static function autoload_plugin($classname)
    {
        // Zend or Google
        $classes = array(
                'Zend_Loader' => 'Zend/Loader.php',
                'phpCAS' => 'CAS.php',
                'MDB2' => 'MDB2.php',
                'PEAR' => 'PEAR.php',
                'Contact_Vcard_Build' => 'File/Contact_Vcard_Build.php',
                'Contact_Vcard_Parse' => 'File/Contact_Vcard_Parse.php',
                'HTTP_Request' => 'HTTP/Request.php',
                'Net_LDAP2' => 'Net/LDAP2.php',
                'Net_LDAP2_Filter' => 'Net/LDAP2/Filter.php',
                'Pager' => 'Pager/Pager.php',
                'Pager_Sliding' => 'Pager/Sliding.php',
                'XML_Unserializer' => 'XML/Unserializer.php',
                'XML_Serializer' => 'XML/Serializer.php',
                'HTML_Table' => 'HTML/Table.php',
                'HTML_QuickForm' => 'HTML/QuickForm.php',
                'HTML_Menu' => 'HTML/Menu.php',
                'HTML_Menu_ArrayRenderer' => 'HTML/Menu/ArrayRenderer.php',
                'HTML_Menu_DirectTreeRenderer' => 'HTML/Menu/DirectTreeRenderer.php',
                'HTML_QuickForm_Controller' => 'HTML/QuickForm/Controller.php',
                'HTML_QuickForm_Rule' => 'HTML/QuickForm/Rule.php',
                'HTML_QuickForm_Page' => 'HTML/QuickForm/Page.php',
                'HTML_QuickForm_Action' => 'HTML/QuickForm/Action.php',
                'HTML_QuickForm_RuleRegistry' => 'HTML/QuickForm/RuleRegistry.php',
                'HTML_QuickForm_Action_Display' => 'HTML/QuickForm/Action/Display.php',
                'HTML_QuickForm_Rule_Compare' => 'HTML/QuickForm/Rule/Compare.php',
                'HTML_QuickForm_advmultiselect' => 'HTML/QuickForm/advmultiselect.php',
                'HTML_QuickForm_button' => 'HTML/QuickForm/button.php',
                'HTML_QuickForm_checkbox' => 'HTML/QuickForm/checkbox.php',
                'HTML_QuickForm_date' => 'HTML/QuickForm/date.php',
                'HTML_QuickForm_element' => 'HTML/QuickForm/element.php',
                'HTML_QuickForm_file' => 'HTML/QuickForm/file.php',
                'HTML_QuickForm_group' => 'HTML/QuickForm/group.php',
                'HTML_QuickForm_hidden' => 'HTML/QuickForm/hidden.php',
                'HTML_QuickForm_html' => 'HTML/QuickForm/html.php',
                'HTML_QuickForm_radio' => 'HTML/QuickForm/radio.php',
                'HTML_QuickForm_select' => 'HTML/QuickForm/select.php',
                'HTML_QuickForm_text' => 'HTML/QuickForm/text.php',
                'HTML_QuickForm_textarea' => 'HTML/QuickForm/textarea.php');

        if (array_key_exists($classname, $classes))
        {
            require_once $classes[$classname];
            return class_exists($classname);
        }

        $other_plugin_classes = array('RestResult' => 'webservices/rest/client/rest_result.class.php');
        if (array_key_exists($classname, $other_plugin_classes))
        {
            require_once Path :: get_plugin_path() . '/' . $other_plugin_classes[$classname];
            return class_exists($classname);
        }

        //Fallback strategy => Pear naming convention : replace _ by /
        $classfile = str_replace("_", "/", $classname) . ".php";
        if (file_exists($classfile))
        {
            require_once $classfile;
            return class_exists($classname);
        }
        return false;
    }

    /**
     * Render a complete backtrace for the currently executing script
     * @return string The backtrace
     */
    static function get_backtrace()
    {
        $html = array();
        $backtraces = debug_backtrace();
        foreach ($backtraces as $backtrace)
        {
            $html[] = implode(' ', $backtrace);
        }
        return implode('<br/>', $html);
    }

    /**
     * Get the class name from a fully qualified namespaced class name
     * if and only if it's in the given namespace
     *
     * @param string $namespace
     * @param string $classname
     * @return string|boolean The class name or false
     */
    static function get_namespace_classname($namespace, $classname)
    {
        $classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            return false;
        }
        else
        {
            $class_name = $classname_parts[count($classname_parts) - 1];
            array_pop($classname_parts);
            if (implode('\\', $classname_parts) != $namespace)
            {
                return false;
            }
            else
            {
                return $class_name;
            }
        }
    }

    /**
     * @param Object $object
     * @param booleean $convert_to_underscores
     * @return string The class name
     */
    static function get_classname_from_object($object, $convert_to_underscores = false)
    {
        return self :: get_classname_from_namespace(get_class($object), $convert_to_underscores);
    }

    /**
     * @param Object $object
     * @return string The namespace
     */
    static function get_namespace_from_object($object)
    {
        return self :: get_namespace_from_classname(get_class($object));
    }

    /**
     * @param string $classname
     * @param boolean $convert_to_underscores
     * @return string The class name
     */
    static function get_classname_from_namespace($classname, $convert_to_underscores = false)
    {
        $classname = array_pop(explode('\\', $classname));

        if ($convert_to_underscores)
        {
            $classname = self :: camelcase_to_underscores($classname);
        }

        return $classname;
    }

    /**
     * @param string $namespace
     * @return string The namespace
     */
    static function get_namespace_from_classname($namespace)
    {
        $namespace_parts = explode('\\', $namespace);
        array_pop($namespace_parts);
        return implode('\\', $namespace_parts);
    }

    static function get_package_name_from_namespace($namespace, $convert_to_camelcase = false)
    {
        $package_name = array_pop(explode('\\', $namespace));

        if ($convert_to_camelcase)
        {
            $package_name = self :: underscores_to_camelcase($package_name);
        }

        return $package_name;
    }

    static function load_custom_class($path_hash, $class_name, $prefix_path)
    {
        $lower_case = self :: camelcase_to_underscores($class_name);

        if (key_exists($lower_case, $path_hash))
        {
            $url = $path_hash[$lower_case];
            require_once $prefix_path . $url;
            return true;
        }

        return false;
    }

    static function handle_exception($exception)
    {
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
        	<head>
        		<title>Uncaught exception</title>
        		<link rel="stylesheet" href="common/libraries/resources/css/aqua/aqua.css" type="text/css"/>
        	</head>
        	<body dir="ltr">
        		<div id="outerframe">
        			<div id="header">
        				<div id="header1">
        					<div class="banner"><span class="logo"></span><span class="text">Chamilo</span></div>
        					<div class="clear">&nbsp;</div>
        				</div>
        				<div class="clear">&nbsp;</div>
        			</div>

                    <div id="trailbox">
                        <ul id="breadcrumbtrail">
                        	<li><a href="#">Uncaught exception</a></li>
                        </ul>
                    </div>

        			<div id="main" style="min-height: 300px;">
        				<div class="error-message">' . $exception->getMessage() . '</div><br /><br />
        			</div>

        			<div id="footer">
        				<div id="copyright">
        					<div class="logo">
        					<a href="http://www.chamilo.org"><img src="common/libraries/resources/images/aqua/logo_footer.png" /></a>
        					</div>
        					<div class="links">
        						<a href="http://www.chamilo.org">http://www.chamilo.org</a>&nbsp;|&nbsp;&copy;&nbsp;2009
        					</div>
        					<div class="clear"></div>
        				</div>
        			</div>
        		</div>
        	</body>
        </html>';
        echo $html;
    }

}
?>