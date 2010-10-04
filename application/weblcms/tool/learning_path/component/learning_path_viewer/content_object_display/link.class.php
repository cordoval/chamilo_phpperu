<?php
/**
 * $Id: link.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer.content_object_display
 */
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class LinkDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($link)
    {
        $html[] = $this->add_tracking_javascript();
        //$html[] = '<h3>' . $link->get_title() . '</h3>' . $link->get_description() . '<br />';
        $html[] = $this->display_link($link->get_url());
        
        return implode("\n", $html);
    }
}

?>