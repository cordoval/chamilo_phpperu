<?php
namespace repository\content_object\learning_path;

use common\libraries\Path;

/**
 * $Id: hotpotatoes.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer.content_object_display
 */
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class LearningPathHotpotatoesContentObjectDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($content_object, $learning_path_item_attempt_data, $continue_url, $previous_url, $jump_urls)
    {
        $link = $content_object->add_javascript(Path :: get(WEB_PATH) . 'application/weblcms/php/ajax/lp_hotpotatoes_save_score.php', null, $learning_path_item_attempt_data['active_tracker']->get_id());
        $html[] = $this->display_link($link);
        
        return implode("\n", $html);
    }
}

?>