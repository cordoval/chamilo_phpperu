<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Utilities;
use common\libraries\SubManager;
use common\libraries\Path;
use common\libraries\Translation;

use Exception;

abstract class ExternalRepositoryComponent extends SubManager
{
    const BROWSER_COMPONENT = 'browser';
    const CREATOR_COMPONENT = 'creator';
    const DOWNLOADER_COMPONENT = 'downloader';
    const EXPORTER_COMPONENT = 'exporter';
    const IMPORTER_COMPONENT = 'importer';
    const VIEWER_COMPONENT = 'viewer';
    const SELECTER_COMPONENT = 'selecter';
    const EDITOR_COMPONENT = 'editor';
    const DELETER_COMPONENT = 'deleter';
    const CONFIGURER_COMPONENT = 'configurer';
    const INTERNAL_SYNCER_COMPONENT = 'internal_syncer';
    const EXTERNAL_SYNCER_COMPONENT = 'external_syncer';
    const NEWFOLDER_COMPONENT = 'newfolder';

    static function factory($type, $application)
    {
        $file = dirname(__FILE__) . '/component/' . $type . '.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ExternalRepositoryComponentTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = __NAMESPACE__ . '\\' . 'ExternalRepositoryComponent' . Utilities :: underscores_to_camelcase($type) . 'Component';
        return new $class($application);
    }

    function get_application_component_path()
    {
        return Path :: get_common_extensions_path() . 'external_repository_manager/php/component/';
    }

    function get_external_repository_object_viewing_url($object)
    {
        return $this->get_parent()->get_external_repository_object_viewing_url($object);
    }

    function count_external_repository_objects($condition)
    {
        return $this->get_parent()->count_external_repository_objects($condition);
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_parent()->retrieve_external_repository_objects($condition, $order_property, $offset, $count);
    }

    function retrieve_external_repository_object($id)
    {
        return $this->get_parent()->retrieve_external_repository_object($id);
    }

    function delete_external_repository_object($id)
    {
        return $this->get_parent()->delete_external_repository_object($id);
    }

    function export_external_repository_object($object)
    {
        return $this->get_parent()->export_external_repository_object($object);
    }

    function import_external_repository_object(ExternalRepositoryObject $object)
    {
        return $this->get_parent()->import_external_repository_object($object);
    }

    function synchronize_internal_repository_object(ExternalRepositoryObject $object)
    {
        return $this->get_parent()->synchronize_internal_repository_object($object);
    }

    function synchronize_external_repository_object(ExternalRepositoryObject $object)
    {
        return $this->get_parent()->synchronize_external_repository_object($object);
    }

    function get_external_repository_object_actions(ExternalRepositoryObject $object)
    {
        return $this->get_parent()->get_external_repository_object_actions($object);
    }

    function get_external_repository()
    {
        return $this->get_parent()->get_external_repository();
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

    function get_repository_type()
    {
        return $this->get_parent()->get_repository_type();
    }

    function get_setting($variable)
    {
        return $this->get_parent()->get_setting($variable);
    }

    function get_user_setting($variable)
    {
        return $this->get_parent()->get_user_setting($variable);
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
        return ExternalRepositoryManager :: DEFAULT_ACTION;
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
        return ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION;
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