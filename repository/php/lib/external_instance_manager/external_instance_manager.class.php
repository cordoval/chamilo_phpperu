<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Path;
use common\libraries\SubManager;
use common\libraries\EqualityCondition;
use common\libraries\OrCondition;
use common\libraries\AndCondition;
use common\libraries\Utilities;

use admin\Registration;
use admin\AdminDataManager;

class ExternalInstanceManager extends SubManager
{
    const PARAM_INSTANCE_ACTION = 'action';
    const PARAM_INSTANCE = 'instance';
    const PARAM_EXTERNAL_TYPE = 'instance_type';
    const PARAM_EXTERNAL_INSTANCE_TYPE = 'type';

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
        return Path :: get_repository_path() . 'lib/external_instance_manager/component/';
    }

    function count_external_instances($condition = null)
    {
        return $this->get_parent()->count_external_instances($condition);
    }

    function retrieve_external_instances($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_external_instances($condition, $offset, $count, $order_property);
    }

    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }

    function retrieve_external_instance($external_instance_id)
    {
        return $this->get_parent()->retrieve_external_instance($external_instance_id);
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

    static function get_registered_types($status = Registration :: STATUS_ACTIVE)
    {
        $instance_conditions = array();
        $instance_conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_EXTERNAL_REPOSITORY_MANAGER);
        $instance_conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_VIDEO_CONFERENCING_MANAGER);

        $conditions = array();
        $conditions[] = new OrCondition($instance_conditions);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_STATUS, $status);

        return AdminDataManager :: get_instance()->retrieve_registrations(new AndCondition($conditions));
    }

    static function get_namespace($instance_type = null, $type = null)
    {
        if (is_null($instance_type) && is_null($type))
        {
            return __NAMESPACE__;
        }
        elseif (! is_null($instance_type) && is_null($type))
        {
            return 'common\extensions\\' . $instance_type;
        }
        elseif (! is_null($instance_type) && ! is_null($type))
        {
            return 'common\extensions\\' . $instance_type . '\implementation\\' . $type;
        }
    }

    static function get_manager_class($type)
    {
        return self :: get_namespace($type) . '\\' . Utilities :: underscores_to_camelcase($type);
    }
    
    static function get_manager_connector_class($type)
    {
        return self :: get_manager_class($type) . 'Connector';
    }
    
    static function exists($instance_type, $type)
    {
        $manager_class = self :: get_manager_class($instance_type);
        return $manager_class :: exists($type);
    }
}
?>