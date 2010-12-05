<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use repository;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\ActionBarSearchForm;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\Utilities;
use common\libraries\ToolbarItem;
use common\libraries\Theme;

use common\extensions\video_conferencing_manager\VideoConferencingObjectRenderer;
use common\extensions\video_conferencing_manager\VideoConferencingManager;
use common\extensions\video_conferencing_manager\VideoConferencingManagerConnector;
use common\extensions\video_conferencing_manager\VideoConferencingObject;

use repository\ExternalSetting;
use repository\ExternalSync;

/**
 * @author Hans De Bisschop
 */
class BbbVideoConferencingManager extends VideoConferencingManager
{
    const VIDEO_CONFERENCING_TYPE = 'bbb';

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_application_component_path()
     */
    function get_application_component_path()
    {
        return Path :: get_common_extensions_path() . 'video_conferencing_manager/implementation/bbb/php/component/';
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#validate_settings()
     */
    function validate_settings($video_conferencing)
    {
        //     	$account_id = ExternalSetting :: get('account_id', $video_conferencing->get_id());
        //        $account_pw = ExternalSetting :: get('account_pw', $video_conferencing->get_id());
        //
        //        if (! $account_id || ! $account_pw)
        //        {
        //            return false;
        //        }
        return true;
    }

    function get_video_conferencing_object_viewing_url(VideoConferencingObject $object)
    {
        $parameters = array();
        //        $parameters[self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self :: PARAM_VIDEO_CONFERENCING_ID] = $object->get_id();
        
        return $this->get_url($parameters);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#support_sorting_direction()
     */
    function support_sorting_direction()
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_menu_items()
     */
    function get_menu_items()
    {
        $menu_items = array();
        
        return $menu_items;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#is_ready_to_be_used()
     */
    function is_ready_to_be_used()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_external_repository_actions()
     */
    function get_video_conferencing_actions()
    {
        $actions = array(self :: ACTION_CREATE_MEETING);
        
        $is_platform = $this->get_user()->is_platform_admin() && (count(ExternalSetting :: get_all($this->get_video_conferencing()->get_id())) > 0);
        
        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_VIDEO_CONFERENCING;
        }
        
        if ($this->get_user()->is_platform_admin())
        {
            $actions[] = self :: ACTION_BROWSER_VIDEO_CONFERENCING;
        }
        
        return $actions;
    }

    /**
     * @return VideoConferencingManagerConnector
     */
    function get_video_conferencing_manager_connector()
    {
        return VideoConferencingManagerConnector :: get_instance($this->get_video_conferencing());
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_available_renderers()
     */
    function get_available_renderers()
    {
        return array(VideoConferencingObjectRenderer :: TYPE_TABLE);
    }

    function get_video_conferencing_object_actions(ExternalSync $external_sync)
    {
        $toolbar_items = array();
        if ($external_sync instanceof ExternalSync)
        {
            $object = $external_sync->get_content_object();
            
            if ($this->get_user()->is_platform_admin() || $object->get_owner_id() == $this->get_user()->get_id())
            {
                if ($external_sync->get_external_object()->is_joinable())
                {
                    $toolbar_items[self :: ACTION_JOIN_MEETING] = new ToolbarItem(Translation :: get('JoinMeeting'), Theme :: get_image_path() . 'action_join.png', $this->get_url(array(
                            self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION => self :: ACTION_JOIN_MEETING, self :: PARAM_VIDEO_CONFERENCING_ID => $external_sync->get_id())), ToolbarItem :: DISPLAY_ICON, false, null, '_blank');
                }
                if ($external_sync->get_external_object()->is_endable())
                {
                    $toolbar_items[self :: ACTION_END_VIDEO_CONFERENCING] = new ToolbarItem(Translation :: get('EndMeeting'), Theme :: get_image_path() . 'action_end.png', $this->get_url(array(
                            self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION => self :: ACTION_END_VIDEO_CONFERENCING, self :: PARAM_VIDEO_CONFERENCING_ID => $external_sync->get_id())), ToolbarItem :: DISPLAY_ICON, true);
                }
            }
        }
        return $toolbar_items;
    }

    /**
     * @return string
     */
    function get_video_conferencing_type()
    {
        return self :: VIDEO_CONFERENCING_TYPE;
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
        return self :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION;
    }
}
?>