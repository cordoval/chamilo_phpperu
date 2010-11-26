<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\ActionBarSearchForm;
use common\libraries\PatternMatchCondition;
use common\libraries\OrCondition;
use common\libraries\Utilities;

use common\extensions\video_conferencing_manager\VideoConferencingObjectRenderer;
use common\extensions\video_conferencing_manager\VideoConferencingManager;
use common\extensions\video_conferencing_manager\VideoConferencingObject;

use repository\ExternalSetting;
use repository\content_object\document\Document;

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
    function get_external_repository_actions()
    {
        $actions = array(self :: ACTION_CREATE_MEETING);

        $is_platform = $this->get_user()->is_platform_admin() && (count(VideoConferencingSetting :: get_all($this->get_video_conferencing()->get_id())) > 0);

        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_VIDEO_CONFERENCING;
        }

        return $actions;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_available_renderers()
     */
    function get_available_renderers()
    {
        return array(VideoConferencingObjectRenderer :: TYPE_TABLE);
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