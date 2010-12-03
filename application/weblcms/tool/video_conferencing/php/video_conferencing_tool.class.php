<?php
namespace application\weblcms\tool\video_conferencing;

use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\extensions\video_conferencing_manager\VideoConferencingManager;
use repository\content_object\bbb_meeting\BbbMeeting;

use application\weblcms\ContentObjectPublicationListRenderer;
use application\weblcms\Tool;
use application\weblcms\WeblcmsRights;

/**
 * $Id: announcement_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.announcement.component
 */

//require_once dirname(__FILE__) . '/announcement_tool_component.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class VideoConferencingTool extends Tool
{
    const ACTION_JOIN = 'joiner';
    const ACTION_END = 'ender';

    static function get_allowed_types()
    {
        return array(BbbMeeting :: get_type_name());
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        return $browser_types;
    }

    function add_content_object_publication_actions($toolbar, $publication)
    {
        $has_edit_right = $this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $publication->get_id());
        $external_sync = $publication->get_content_object()->get_synchronization_data();
        
        if ($external_sync->get_external_object())
        {
            if ($has_edit_right)
            {
                if ($external_sync->get_external_object()->is_endable())
                {
                    $parameters = array();
                    
                    $parameters[VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION] = VideoConferencingManager :: ACTION_END_VIDEO_CONFERENCING;
                    $parameters[VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_ID] = $external_sync->get_id();
                    $parameters[self :: PARAM_ACTION] = self :: ACTION_END;
                    $parameters[self :: PARAM_PUBLICATION_ID] = $publication->get_id();
                    $toolbar->prepend_item(new ToolbarItem(Translation :: get('EndMeeting'), Theme :: get_image_path() . 'action_end.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON, true));
                }
            }
            if ($external_sync->get_external_object()->is_joinable())
            {
                $parameters = array();
                
                $parameters[VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION] = VideoConferencingManager :: ACTION_JOIN_MEETING;
                $parameters[VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_ID] = $external_sync->get_id();
                $parameters[self :: PARAM_ACTION] = self :: ACTION_JOIN;
                $parameters[self :: PARAM_PUBLICATION_ID] = $publication->get_id();
                
                $toolbar->prepend_item(new ToolbarItem(Translation :: get('JoinMeeting'), Theme :: get_image_path() . 'action_join.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON, false, null, '_blank'));
            }
        }
    
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }
}
?>