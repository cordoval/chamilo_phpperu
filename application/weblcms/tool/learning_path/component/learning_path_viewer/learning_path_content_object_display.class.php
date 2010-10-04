<?php
/**
 * $Id: learning_path_content_object_display.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer
 */

class LearningPathContentObjectDisplay
{
    private $parent;

    public static function factory($parent, $type)
    {
        $class = ContentObject :: type_to_class($type) . 'Display';
        $file = dirname(__FILE__) . '/content_object_display/' . $type . '.class.php';
        
        if (file_exists($file))
        {
            require_once $file;
            return new $class($parent);
        }
        else
            return new self($parent);
    }

    function LearningPathContentObjectDisplay($parent)
    {
        $this->parent = $parent;
    }

    function get_parent()
    {
        return $this->parent;
    }

    function display_content_object($object)
    {
        $display = ContentObjectDisplay :: factory($object);
        return $display->get_full_html() . "\n" . $this->add_tracking_javascript();
    }

    function add_tracking_javascript()
    {
        $trackers = $this->get_parent()->get_trackers();
        $tracker_id = $trackers['lpi_tracker']->get_id();
        $trackers['lpi_tracker']->set_status('completed');
        $trackers['lpi_tracker']->update();
        
        $html[] = '<script languages="JavaScript">';
        $html[] = '    var tracker_id = ' . $tracker_id . ';';
        $html[] = '</script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_APP_PATH) . 'lib/weblcms/tool/learning_path/javascript/learning_path_item.js');
        
        return implode("\n", $html);
    }

    protected function display_link($link)
    {
        $html[] = '<iframe frameborder="0" class="link_iframe" src="' . $link . '" width="100%" height="700px">';
        $html[] = '<p>Your browser does not support iframes.</p></iframe>';
        
        return implode("\n", $html);
    }

    protected function display_box($info)
    {
        return '<div style="position: relative; margin: 10px auto; margin-left: -350px; width: 700px;
				left: 50%; right: 50%; border-width: 1px; border-style: solid;
				background-color: #E5EDF9; border-color: #4171B5; padding: 15px; text-align:center;">' . $info . '</div>';
    }
}

?>