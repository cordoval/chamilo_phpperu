<?php
/**
 * $Id: package_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/package_manager_component.class.php';

class PackageManager extends SubManager
{
    const PARAM_PACKAGE_ACTION = 'action';
    const PARAM_REGISTRATION = 'registration';
    const PARAM_ACTIVATE_SELECTED = 'activate';
    const PARAM_DEACTIVATE_SELECTED = 'deactivate';
    const PARAM_INSTALL_SELECTED = 'install';
    const PARAM_PACKAGE = 'package';
    const PARAM_INSTALL_TYPE = 'type';
    const PARAM_SECTION = 'section';
    
    const ACTION_BROWSE_PACKAGES = 'browse';
    const ACTION_ACTIVATE_PACKAGE = 'activate';
    const ACTION_DEACTIVATE_PACKAGE = 'deactivate';
    const ACTION_REMOTE_PACKAGE = 'remote';
    const ACTION_LOCAL_PACKAGE = 'local';
    const ACTION_ARCHIVE_PACKAGE = 'archive';
    const ACTION_SYNCHRONISE_REMOTE_PACKAGES = 'synchronise';
    const ACTION_INSTALL_PACKAGE = 'install';
    const ACTION_REMOVE_PACKAGE = 'remove';
    
    const INSTALL_REMOTE = 'remote';
    const INSTALL_ARCHIVE = 'archive';
    const INSTALL_LOCAL = 'local';

    function PackageManager($admin_manager)
    {
        parent :: __construct($admin_manager);
        
        $package_action = Request :: get(self :: PARAM_PACKAGE_ACTION);
        if ($package_action)
        {
            $this->set_parameter(self :: PARAM_PACKAGE_ACTION, $package_action);
        }
    }

    function run()
    {
        $package_action = $this->get_parameter(self :: PARAM_PACKAGE_ACTION);
        
        switch ($package_action)
        {
            case self :: ACTION_BROWSE_PACKAGES :
                $component = PackageManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_ACTIVATE_PACKAGE :
                $component = PackageManagerComponent :: factory('Activator', $this);
                break;
            case self :: ACTION_DEACTIVATE_PACKAGE :
                $component = PackageManagerComponent :: factory('Deactivator', $this);
                break;
            case self :: ACTION_REMOTE_PACKAGE :
                $component = PackageManagerComponent :: factory('Remote', $this);
                break;
            case self :: ACTION_SYNCHRONISE_REMOTE_PACKAGES :
                $component = PackageManagerComponent :: factory('Synchroniser', $this);
                break;
            case self :: ACTION_INSTALL_PACKAGE :
                $component = PackageManagerComponent :: factory('Installer', $this);
                break;
            case self :: ACTION_LOCAL_PACKAGE :
                $component = PackageManagerComponent :: factory('Local', $this);
                break;
            case self :: ACTION_REMOVE_PACKAGE :
                $component = PackageManagerComponent :: factory('Remover', $this);
                break;
            default :
                $component = PackageManagerComponent :: factory('Browser', $this);
                break;
        }
        
        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_admin_path() . 'lib/package_manager/component/';
    }

    function retrieve_registration($id)
    {
        return $this->get_parent()->retrieve_registration($id);
    }

    function retrieve_registrations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_registrations($condition, $order_by, $offset, $max_objects);
    }

    function count_registrations($condition = null)
    {
        return $this->get_parent()->count_registrations($condition);
    }

    function get_registration_activation_url($registration)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_ACTIVATE_PACKAGE, self :: PARAM_REGISTRATION => $registration->get_id()));
    }

    function get_registration_deactivation_url($registration)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_DEACTIVATE_PACKAGE, self :: PARAM_REGISTRATION => $registration->get_id()));
    }

    function get_registration_removal_url($registration)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_REMOVE_PACKAGE, self :: PARAM_SECTION => $registration->get_type(), self :: PARAM_PACKAGE => $registration->get_id()));
    }

    function get_remote_package_installation_url($remote_package)
    {
        return $this->get_url(array(self :: PARAM_PACKAGE_ACTION => self :: ACTION_INSTALL_PACKAGE, self :: PARAM_INSTALL_TYPE => self :: INSTALL_REMOTE, self :: PARAM_PACKAGE => $remote_package->get_id()));
    }

    /**
     * @see AdminManager :: count_remote_packages()
     */
    function count_remote_packages($condition = null)
    {
        return $this->get_parent()->count_remote_packages($condition);
    }

    /**
     * @see AdminManager :: retrieve_remote_package()
     */
    function retrieve_remote_package($id)
    {
        return $this->get_parent()->retrieve_remote_package($id);
    }

    /**
     * @see AdminManager :: retrieve_remote_packages()
     */
    function retrieve_remote_packages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_remote_packages($condition, $order_by, $offset, $max_objects);
    }
}
?>