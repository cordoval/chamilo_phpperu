<?php
/**
 * $Id: scorm_item.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer.content_object_display
 */
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class ScormItemDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($scorm_item, $tracker_attempt_data, $continue_url, $previous_url, $jump_urls)
    {
        //dump($tracker_attempt_data);
        if ($tracker_attempt_data['active_tracker'])
        {
            $id = $tracker_attempt_data['active_tracker']->get_id();
            $tracker_attempt_data['active_tracker']->set_start_time(time());
            $tracker_attempt_data['active_tracker']->update();
        }
        
        $html[] = '<script language="JavaScript" type="text/javascript">var tracker_id = ' . $id;
        $html[] = 'var continue_url = "' . $continue_url . '";';
        $html[] = 'var previous_url = "' . $previous_url . '";';
        
        $html[] = 'var jump_urls = new Array();';
        
        foreach ($jump_urls as $identifier => $jump_url)
        {
            $html[] = 'jump_urls["' . $identifier . '"] = "' . $jump_url . '";';
        }
        
        $html[] = '</script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_APP_PATH) . 'lib/weblcms/tool/learning_path/javascript/scorm/chamilo_api.js');
        //$html[] = urldecode($scorm_item->get_url(true));
        $html[] = $this->display_link(urldecode($scorm_item->get_url(true)));
        
        return implode("\n", $html);
    }
}

?>