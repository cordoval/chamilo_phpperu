<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Filesystem;

use common\libraries\fedora_fs_store;
use common\libraries\Path;
use common\libraries\Utilities;
use repository\ExternalSetting;
use common\extensions\external_repository_manager\ExternalRepositoryManager;
use common\extensions\external_repository_manager\ExternalRepositoryObject;
use common\libraries\DatetimeUtilities;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use repository\ContentObject;
use common\libraries\Session;
use common\libraries\AndCondition;
use repository\RepositoryDataManager;
use common\libraries\Request;
use repository\ExternalSync;

require_once dirname(__FILE__) . '/fedora_external_repository_manager_connector.class.php';

/**
 * Manager of the Fedora repository action.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManager extends ExternalRepositoryManager
{

    const REPOSITORY_TYPE = 'fedora';

    const ACTION_EXPORT_COURSE = 'course_exporter';

    const PARAM_DATASTREAM_ID = 'datastream_id';
    const PARAM_EXPORT_FORMAT = 'export_format';
    const PARAM_COURSE_ID = 'course_id';
    const PARAM_WIZARD_ACTION = 'wizard_action';

    /**
     * If datetime format is not specified for the current language default to d.m.y, H:M.
     *
     * @return string
     */
    public static function get_date_time_format()
    {
        $date_format = Translation :: get('DateFormatShort');
        $date_format = $date_format == 'DateFormatShort' ? '%d.%m.%Y' : $date_format;
        $time_format = Translation :: get('TimeNoSecFormat');
        $time_format = $time_format == 'TimeNoSecFormat' ? '%H:%M' : $time_format;
        $result = $date_format . ', ' . $time_format;
        return $result;
    }

    /**
     * Format a date value to a local string based on the use language. If no date time format is provided for the language defaults to d.m.y, H:M.
     * @param <type> $value
     * @return <type>
     */
    public static function format_locale_date($value)
    {
        return DatetimeUtilities :: format_locale_date(self :: get_date_time_format(), $value);
    }

    /**
     * Delete the synchronization data for an external object with an id of $id.
     * This typically happens if the object is deleted in the external repository.
     *
     * @param string $id object id
     * @param string|bool $external_instance the instance id of the external repository. If false defaults to the request's value.
     * @return bool
     */
    public static function delete_synchronization_data($id, $external_instance = false)
    {
        if (empty($external_instance))
        {
            $external_instance = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
        }

        $sync_conditions = array();
        $sync_conditions[] = new EqualityCondition(ExternalSync :: PROPERTY_EXTERNAL_OBJECT_ID, $id);
        $sync_conditions[] = new EqualityCondition(ExternalSync :: PROPERTY_EXTERNAL_ID, $external_instance);
        $sync_conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id(), ContentObject :: get_table_name());
        $sync_condition = new AndCondition($sync_conditions);

        $synchronization_data = RepositoryDataManager :: get_instance()->retrieve_external_sync($sync_condition);
        $result = $synchronization_data ? $synchronization_data->delete() : false;
        return $result;
    }

    /**
     * @param Application $application
     */
    function __construct($external_repository, $application = null)
    {
        parent :: __construct($external_repository, $application);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_application_component_path()
     */
    function get_application_component_path()
    {
        $result = dirname(__FILE__) . '/component/';
        return $result;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#validate_settings()
     */
    function validate_settings($external_repository)
    {
        return true;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#support_sorting_direction()
     */
    function support_sorting_direction()
    {
        return false;
    }

    /**
     * @param ExternalRepositoryObject $object
     * @return string
     */
    function get_external_repository_object_viewing_url(ExternalRepositoryObject $object)
    {
        $parameters = array();
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        return $this->get_url($parameters);
    }

    /**
     * @return array
     */
    function get_menu_items($parent = null)
    {
        $connector = $this->get_external_repository_manager_connector();
        if (is_array($parent))
        {
            $result = array();
            foreach ($parent as $child)
            {
                $result[] = $this->get_menu_items($child);
            }
            return $result;
        }
        else
            if (empty($parent))
            {
                $parent = $connector->get_store();
                $result = $this->get_menu_items($parent->get_children());
                return $result;
            }
            else
            {
                $result = array();
                $result['title'] = $parent->get_title();
                $result['class'] = $parent->get_class();
                if ($parent instanceof fedora_fs_store)
                {
                    $result['url'] = $parent->is_aggregate() ? $this->get_url(array(
                            self :: PARAM_FOLDER => $parent->get_fsid())) : '#';
                    $result['sub'] = $this->get_menu_items($parent->get_children());
                }
                else
                {
                    $result['url'] = $this->get_url(array(self :: PARAM_FOLDER => $parent->get_fsid()));
                }
                return $result;
            }
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
        $result = array(
                self :: ACTION_BROWSE_EXTERNAL_REPOSITORY,
                self :: ACTION_EXPORT_COURSE,
                self :: ACTION_EXPORT_EXTERNAL_REPOSITORY,
                self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY);
        return $result;
    }

    /**
     * List all apis available.
     */
    function get_apis()
    {
        $path = $this->get_application_component_path() . '/api/';
        $result = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
        return $result;
    }

    /**
     * Returns the api selected for the repository.
     */
    function get_api()
    {
        $connector = $this->get_external_repository_manager_connector();
        $external_repository_id = $connector->get_external_repository_instance_id();
        return ExternalSetting :: get('Api', $external_repository_id);
    }

    /**
     * Returns the component's location for the current api.
     */
    function get_api_component_path()
    {
        $result = dirname(__FILE__) . '/component/api/' . $this->get_api() . '/component/';
        return $result;
    }

    /**
     * Returns a new component for the api if one exists. False otherwise.
     *
     * @param $action the action to create a component for. If not provided defaults to the current action.
     * @param $application the application to create a component for. If not provided defaults to $this.
     * @return a new componet or false if none exists.
     */
    function create_api_component($action = false, $application = null)
    {
        $action = $action ? $action : $this->get_action();
        $application = $application ? $application : $this;
        $file = $this->get_api_component_path() . $action . '.class.php';
        if (! file_exists($file) || ! is_file($file))
        {
            return false;
        }
        else
        {
            require_once $file;
            $class = __NAMESPACE__ . '\Fedora' . ucfirst($this->get_api()) . Utilities :: underscores_to_camelcase($action) . 'Component';
            $result = new $class($application->get_parent(), $application->get_external_repository());

            return $result;
        }
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryManager#get_content_object_type_conditions()
     */
    function get_content_object_type_conditions()
    {
        return null;
    }

    /**
     * @return string
     */
    function get_repository_type()
    {
        return self :: REPOSITORY_TYPE;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class::DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self::DEFAULT_ACTION in the context of this class
     * - YourSubManager::DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class::PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self::PARAM_ACTION in the context of this class
     * - YourSubManager::PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION;
    }

}

?>