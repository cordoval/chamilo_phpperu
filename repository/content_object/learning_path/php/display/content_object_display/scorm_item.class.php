<?php
namespace repository\content_object\learning_path;

use common\libraries\ResourceManager;
use common\libraries\Path;

/**
 * $Id: scorm_item.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer.content_object_display
 */
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class LearningPathScormItemContentObjectDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($content_object, $learning_path_item_attempt_data, $continue_url, $previous_url, $jump_urls)
    {
        if ($tracker_attempt_data['active_tracker'])
        {
            $id = $tracker_attempt_data['active_tracker']->get_id();
            $tracker_attempt_data['active_tracker']->set_start_time(time());
            $tracker_attempt_data['active_tracker']->update();
        }
        
        $html[] = '<script type="text/javascript">var tracker_id = ' . $id;
        $html[] = 'var continue_url = "' . $continue_url . '";';
        $html[] = 'var previous_url = "' . $previous_url . '";';
        
        $html[] = 'var jump_urls = new Array();';
        
        foreach ($jump_urls as $identifier => $jump_url)
        {
            $html[] = 'jump_urls["' . $identifier . '"] = "' . $jump_urls . '";';
        }
        
        $html[] = '</script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . '/application/weblcms/tool/learning_path/resources/javascript/scorm/chamilo_api.js');
        $html[] = $this->display_link(urldecode($content_object->get_url(true)));
        
        return implode("\n", $html);
    }
}

?>