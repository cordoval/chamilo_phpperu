<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\SubManager;

use Exception;

abstract class VideoConferencingComponent extends SubManager
{

    static function factory($type, $application)
    {
        $file = dirname(__FILE__) . '/component/' . $type . '.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('VideoConferencingComponentTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = __NAMESPACE__ . '\\' . 'VideoConferencingComponent' . Utilities :: underscores_to_camelcase($type) . 'Component';
        return new $class($application);
    }

    function get_application_component_path()
    {
        return Path :: get_common_extensions_path() . 'video_conferencing_manager/php/component/';
    }

    function count_video_conferencing_objects($condition)
    {
        return $this->get_parent()->count_video_conferencing_objects($condition);
    }

    function retrieve_video_conferencing_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_parent()->retrieve_video_conferencing_objects($condition, $order_property, $offset, $count);
    }

    function retrieve_video_conferencing_object(ExternalSync $external_sync)
    {
        return $this->get_parent()->retrieve_video_conferencing_object($external_sync);
    }

    function import_video_conferencing_object(VideoConferencingObject $object)
    {
        return $this->get_parent()->import_video_conferencing_object($object);
    }

    function get_video_conferencing_object_actions(VideoConferencingObject $object)
    {
        return $this->get_parent()->get_video_conferencing_object_actions($object);
    }

    function get_video_conferencing()
    {
        return $this->get_parent()->get_video_conferencing();
    }

    function get_content_object_type_conditions()
    {
        return $this->get_parent()->get_content_object_type_conditions();
    }

    function support_sorting_direction()
    {
        return $this->get_parent()->support_sorting_direction();
    }

    function translate_search_query($query)
    {
        return $this->get_parent()->translate_search_query($query);
    }

    function get_menu_items()
    {
        return $this->get_parent()->get_menu_items();
    }

    function get_video_conferencing_type()
    {
        return $this->get_parent()->get_video_conferencing_type();
    }

    function get_setting($variable)
    {
        return $this->get_parent()->get_setting($variable);
    }

    function get_user_setting($variable)
    {
        return $this->get_parent()->get_user_setting($variable);
    }

    function get_video_conferencing_object_viewing_url($object)
    {
        return $this->get_parent()->get_video_conferencing_object_viewing_url($object);
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
        return VideoConferencingManager :: DEFAULT_ACTION;
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
        return VideoConferencingManager :: PARAM_VIDEO_CONFERENCING_MANAGER_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application, false);
    }
}
?>