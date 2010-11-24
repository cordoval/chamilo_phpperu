<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Path;
use common\libraries\SubManager;

class VideoConferencingInstanceManager extends SubManager
{
    const PARAM_INSTANCE_ACTION = 'action';
    const PARAM_INSTANCE = 'instance';
    const PARAM_VIDEO_CONFERENCING_TYPE = 'type';

    const ACTION_BROWSE_INSTANCES = 'browser';
    const ACTION_ACTIVATE_INSTANCE = 'activator';
    const ACTION_DEACTIVATE_INSTANCE = 'deactivator';
    const ACTION_UPDATE_INSTANCE = 'updater';
    const ACTION_DELETE_INSTANCE = 'deleter';
    const ACTION_CREATE_INSTANCE = 'creator';
    const ACTION_MANAGE_INSTANCE_RIGHTS = 'rights_editor';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_INSTANCES;

    function __construct($repository_manager)
    {
        parent :: __construct($repository_manager);

        $instance_action = Request :: get(self :: PARAM_INSTANCE_ACTION);
        if ($instance_action)
        {
            $this->set_action($instance_action);
        }
    }

    function set_action($action)
    {
        $this->set_parameter(self :: PARAM_INSTANCE_ACTION, $action);
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_INSTANCE_ACTION);
    }

    function get_application_component_path()
    {
        return Path :: get_repository_path() . 'lib/video_conferencing_instance_manager/component/';
    }

    function count_videos_conferencing($condition = null)
    {
        return $this->get_parent()->count_videos_conferencing($condition);
    }

    function retrieve_videos_conferencing($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_videos_conferencing($condition, $offset, $count, $order_property);
    }

    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }

    function retrieve_video_conferencing($video_conferencing_id)
    {
        return $this->get_parent()->retrieve_video_conferencing($video_conferencing_id);
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
        return self :: PARAM_INSTANCE_ACTION;
    }
}
?>