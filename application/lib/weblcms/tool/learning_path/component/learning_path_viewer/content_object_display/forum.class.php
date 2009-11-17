<?php
/**
 * $Id: forum.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer.content_object_display
 */
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class ForumDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($forum)
    {
        $html[] = $this->add_tracking_javascript();
        $link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_CLO, 'pid' => $forum->get_id()));
        $html[] = $this->display_link($link);
        
        return implode("\n", $html);
    }
}

?>