<?php
/**
 * $Id: hotpotatoes.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer.content_object_display
 */
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class HotpotatoesDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($hp, $tracker_attempt_data)
    {
        $lpi_attempt_id = $tracker_attempt_data['active_tracker']->get_id();
        
        $link = $hp->add_javascript(Path :: get(WEB_PATH) . 'application/lib/weblcms/ajax/lp_hotpotatoes_save_score.php', null, $lpi_attempt_id);
        $html[] = $this->display_link($link);
        
        return implode("\n", $html);
    }
}

?>